<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ERP Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            overflow-x: hidden;
            background-color: #f8f9fa;
        }

        #sidebar-wrapper {
            min-height: 100vh;
            width: 250px;
            background-color: #212529;
            transition: all 0.3s;
        }

        #sidebar-wrapper .sidebar-heading {
            padding: 1.5rem;
            font-size: 1.2rem;
            color: #fff;
            font-weight: bold;
            border-bottom: 1px solid #343a40;
        }

        #sidebar-wrapper .list-group-item {
            background: none;
            border: none;
            color: #c2c7d0;
            padding: 0.75rem 1.5rem;
        }

        #sidebar-wrapper .list-group-item:hover,
        #sidebar-wrapper .list-group-item.active {
            background-color: #343a40;
            color: #fff;
        }

        #page-content-wrapper {
            width: 100%;
        }

        @media print {

            #sidebar-wrapper,
            .navbar,
            .btn,
            .no-print {
                display: none !important;
            }

            #page-content-wrapper {
                width: 100% !important;
                padding: 0 !important;
            }
        }
    </style>
</head>

<body>
    <div class="d-flex" id="wrapper">
        <div id="sidebar-wrapper">
            <div class="sidebar-heading"><i class="fa-solid fa-gears me-2"></i> Industrial ERP</div>
            <div class="list-group list-group-flush mt-3">
                <a href="#"
                    class="list-group-item list-group-item-action {{ Request::is('dashboard*') ? 'active' : '' }}"><i
                        class="fa-solid fa-gauge me-2"></i> Dashboard</a>
                <a href="#" class="list-group-item list-group-item-action"><i
                        class="fa-solid fa-users-gear me-2"></i> Users</a>
                <a href="#" class="list-group-item list-group-item-action"><i
                        class="fa-solid fa-address-book me-2"></i> Customers</a>
                <a href="#" class="list-group-item list-group-item-action"><i
                        class="fa-solid fa-boxes-stacked me-2"></i> Products</a>
                <a href="#" class="list-group-item list-group-item-action"><i
                        class="fa-solid fa-file-invoice-dollar me-2"></i> Invoices</a>
            </div>
        </div>

        <div id="page-content-wrapper">
            <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom px-4 no-print">
                <span class="navbar-brand mb-0 h1">Admin Dashboard</span>
                <div class="ms-auto">
                    <span class="me-3 text-muted"><i class="fa-solid fa-user me-1"></i> Admin</span>
                    <a href="#" class="btn btn-outline-danger btn-sm"><i class="fa-solid fa-sign-out-alt"></i>
                        Logout</a>
                </div>
            </nav>

            <div class="container-fluid p-4">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>

</html>
