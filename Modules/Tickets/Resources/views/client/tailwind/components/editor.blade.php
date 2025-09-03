<script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.4/tinymce.min.js" referrerpolicy="origin"></script>
@php($plugins = 'preview importcss searchreplace autolink autosave save directionality code visualblocks visualchars fullscreen image link media template codesample table charmap pagebreak nonbreaking anchor insertdatetime advlist lists wordcount help charmap quickbars emoticons')
@php($toolbar = 'undo redo | bold italic underline strikethrough | fontfamily fontsize blocks | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist | forecolor backcolor removeformat | pagebreak | charmap emoticons | fullscreen  preview save print | insertfile image media template link anchor codesample | ltr rtl')
@php($menubar = 'file edit view insert format tools table help')
<script>
    if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        tinymce.init({ 
            selector: 'textarea',
            language: @json(app()->getLocale()),
            menubar: @json($menubar),
            plugins: @json($plugins),
            toolbar: @json($toolbar),
            skin: 'oxide-dark',
            content_css: 'dark',
            toolbar_sticky: true,
            imagetools_cors_hosts: ['picsum.photos'],
            autosave_ask_before_unload: true,
            autosave_interval: "30s",
            autosave_restore_when_empty: false,
            autosave_retention: "2m",
            image_advtab: true,
            invalid_elements: 'script,iframe',
            promotion: false,
            branding: false
        });
    } else {
        tinymce.init({
            selector: 'textarea',
            language: @json(app()->getLocale()),
            menubar: @json($menubar),
            plugins: @json($plugins),
            toolbar: @json($toolbar),
            toolbar_sticky: true,
            imagetools_cors_hosts: ['picsum.photos'],
            autosave_ask_before_unload: true,
            autosave_interval: "30s",
            autosave_restore_when_empty: false,
            autosave_retention: "2m",
            image_advtab: true,
            invalid_elements: 'script,iframe',
            promotion: false,
            branding: false
        });
    }
</script>
