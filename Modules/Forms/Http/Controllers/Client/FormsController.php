<?php
namespace Modules\Forms\Http\Controllers\Client;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Routing\Controller;
use Nwidart\Modules\Facades\Module;
use Modules\Forms\Entities\Form;
use Modules\Forms\Entities\Submission;
use App\Models\Gateways\Gateway;
use Illuminate\Http\Request;
use App\Facades\Captcha;
use App\Models\Payment;

class FormsController extends Controller
{
    public function __construct()
    {
        Captcha::setConfig();
    }

    public function view(Form $form)
    {
        if(!$form->active) {
            return redirect()->route('dashboard')->withError('This form is not active.');
        }

        if(!$form->guest && auth()->guest()) {
            return redirect()->route('login')->withError('You must be logged in to view this page.');
        }

        if($form->required_packages AND is_array($form->required_packages) AND auth()->user()) {
            $hasOrder = auth()->user()->orders()->where('status', 'active')->whereIn('package_id', $form->required_packages)->exists();
            if(!$hasOrder) {
                return redirect()->route('dashboard')->withError('You must have the required package to view this page.');
            }
        }

        if($form->isPaid() AND auth()->guest()) {
            return redirect()->route('login')->withError('You must be logged to complete the payment.');
        }
        
        return view('forms::client.view-form', compact('form'));
    }

    public function submit(Request $request, Form $form)
    {
        if(!$form->active) {
            return redirect()->route('dashboard')->withError('This form is not active.');
        }

        if(!$form->guest && auth()->guest()) {
            return redirect()->route('login')->withError('You must be logged in to view this page.');
        }

        if($form->required_packages AND is_array($form->required_packages) AND auth()->user()) {
            $hasOrder = auth()->user()->orders()->where('status', 'active')->whereIn('package_id', $form->required_packages)->exists();
            if(!$hasOrder) {
                return redirect()->route('dashboard')->withError('You must have the required package to view this page.');
            }
        }

        if($form->max_submissions AND $form->submissions()->count() >= $form->max_submissions) {
            return redirect()->back()->withError('This form has reached the maximum number of submissions.');
        }

        if($form->max_submissions_per_user AND auth()->user()) {
            $userSubmissions = $form->submissions()->where('user_id', auth()->id())->count();
            if($userSubmissions >= $form->max_submissions_per_user) {
                return redirect()->back()->withError('You have reached the maximum number of submissions for this form.');
            }
        }

        $request->validate(array_merge($form->fieldRules(), ['guest_email' => 'sometimes|email']));

        $data = $request->only(array_merge($form->fieldNames(), ['guest_email', 'cf-turnstile-response' => Captcha::CloudFlareRules('page_login')]));

        $submission = $form->submissions()->create([
            'user_id' => auth()->id() ?? null,
            'guest_email' => auth()->guest() ? $request->get('guest_email', null) : null,
            'status' => $form->isPaid() ? 'awaiting_payment' : 'open',
            'data' => $data,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'paid' => false,
        ]);

        $submission->notifyNewSubmission();

        if($form->can_view_submission) {
            return redirect()->route('forms.view-submission', $submission->token)->withSuccess('Form submitted successfully.');
        }

        return redirect()->back()->withSuccess('Form submitted successfully.');
    }

    public function viewSubmission(Submission $submission)
    {
        if($submission->user AND !auth()->user()) {
            return redirect()->route('login')->withError('Please login to view this page.');
        }

        return view('forms::client.view-submission', compact('submission'));
    }

    public function paySubmission(Request $request, Submission $submission)
    {
        if(!$submission->form->isPaid()) {
            return redirect()->back()->withError('This form does not require payment.');
        }

        if($submission->paid) {
            return redirect()->back()->withError('This submission has already been paid.');
        }

        $request->validate([
            'gateway' => 'required|exists:gateways,id',
        ]);

        $gateway = Gateway::findOrFail($request->gateway);
        $payment = Payment::generate([
            'user_id' => auth()->user() ? auth()->id() : null,
            'description' => $submission->form->name,
            'amount' => $submission->form->price,
            'options' => ['submission_id' => $submission->id],
            'handler' => \Modules\Forms\Handlers\PaymentHandler::class,
        ]);

        return redirect()->route('invoice.pay', ['payment' => $payment->id, 'gateway' => request()->input('gateway')]);
    }

    public function updateSubmission(Request $request, Submission $submission)
    {
        $submission->status = $request->get('status');
        $submission->save();

        // send email to user
        if($request->get('email')) {
            $submission->emailUser([
                'subject' => 'Your submission has been updated',
                'content' => $request->get('email'),
            ]);
        }

        return redirect()->back()->withSuccess('Submission updated successfully.');
    }

    public function deleteSubmission(Submission $submission)
    {
        $submission->delete();

        return redirect()->route('admin.forms.submissions.index')->withSuccess('Submission deleted successfully.');
    }

    public function postMessage(Request $request, Submission $submission)
    {
        if($submission->user AND !auth()->user()) {
            return redirect()->route('login')->withError('Please login to view this page.');
        }
        
        if(!$submission->form->can_respond) {
            return redirect()->back()->withError('This form does not allow responses.');
        }

        $request->validate([
            'message' => 'required|min:3|max:5000',
        ]);

        $message = $submission->messages()->create([
            'user_id' => auth()->id() ?? null,
            'guest_email' => auth()->guest() ? $submission->guest_email : null,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'message' => $request->get('message'),
        ]);

        $message->notifyNewMessage();

        $submission->update(['updated_at' => now()]);

        return redirect()->back();
    }
}
