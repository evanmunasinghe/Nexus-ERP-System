<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'ERP Admin Panel' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { overflow-x: hidden; background-color: #f8f9fa; }
        #wrapper { display: flex; width: 100%; }
        #page-content-wrapper { width: 100%; }
        @media print {
            .no-print { display: none !important; }
            #page-content-wrapper { width: 100% !important; padding: 0 !important; }
        }
    </style>
</head>
<body>
    <div id="wrapper">
        <x-sidebar />

        <div id="page-content-wrapper">
            <x-navbar />

            <div class="container-fluid p-4">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show no-print" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                {{ $slot }}
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        (() => {
            const loadSearchResults = async (url, form, shouldPushState = true) => {
                const targetSelector = form.dataset.searchTarget;
                const target = document.querySelector(targetSelector);

                if (! target) {
                    window.location.href = url;
                    return;
                }

                form.querySelectorAll('button, input, a').forEach((element) => {
                    element.classList.add('disabled');
                    element.setAttribute('aria-disabled', 'true');
                });
                target.setAttribute('aria-busy', 'true');

                try {
                    const response = await fetch(url, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                    });

                    if (! response.ok) {
                        throw new Error('Search request failed.');
                    }

                    const html = await response.text();
                    const documentFragment = new DOMParser().parseFromString(html, 'text/html');
                    const nextTarget = documentFragment.querySelector(targetSelector);
                    const nextForm = documentFragment.getElementById(form.id);

                    if (! nextTarget || ! nextForm) {
                        throw new Error('Search response was missing expected content.');
                    }

                    target.outerHTML = nextTarget.outerHTML;
                    form.outerHTML = nextForm.outerHTML;

                    if (shouldPushState) {
                        window.history.pushState({}, '', url);
                    }
                } catch (error) {
                    window.location.href = url;
                }
            };

            document.addEventListener('submit', (event) => {
                const form = event.target.closest('form[data-ajax-search]');

                if (! form) {
                    return;
                }

                event.preventDefault();

                const url = new URL(form.action, window.location.origin);
                const formData = new FormData(form);
                const search = String(formData.get('search') ?? '').trim();

                if (search !== '') {
                    url.searchParams.set('search', search);
                } else {
                    url.searchParams.delete('search');
                }

                loadSearchResults(url.toString(), form);
            });

            document.addEventListener('click', (event) => {
                const link = event.target.closest('a[data-ajax-clear]');

                if (! link) {
                    return;
                }

                const form = link.closest('form[data-ajax-search]');

                if (! form) {
                    return;
                }

                event.preventDefault();

                loadSearchResults(link.href, form);
            });

            window.addEventListener('popstate', () => {
                const form = document.querySelector('form[data-ajax-search]');

                if (form) {
                    loadSearchResults(window.location.href, form, false);
                }
            });
        })();
    </script>
    {{ $scripts ?? '' }}
</body>
</html>
