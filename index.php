
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Hola con Bootstrap</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .animated-text {
            animation: fadeIn 2s ease-in-out;
        }

        @keyframes fadeIn {
            0% {
                opacity: 0;
                transform: translateY(-20px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animated-text:hover {
            animation: hoverBounce 0.5s ease-in-out;
        }

        @keyframes hoverBounce {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-10px);
            }
        }

        .card:hover {
            transform: scale(1.05);
            transition: transform 0.3s ease-in-out;
        }
    </style>
</head>
<body class="bg-light d-flex justify-content-center align-items-center" style="height: 100vh;">
<div class="container text-center">
    <div class="card shadow-lg">
        <div class="card-body">
            <h1 class="animated-text text-primary">CHE, Ursol Gleb, Hoy es <?php echo date('l jS \of F Y h:i:s A'); ?></h1>
            <p class="lead text-muted">"La vida, como el código, se escribe, se corrige y se refactoriza constantemente. Los errores (bugs) son parte del proceso, y aprender a depurarlos es la clave para crecer."</p>
        </div>
    </div>
</div>

<!-- Bootstrap JS and dependencies -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
</body>
</html>

