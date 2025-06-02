"# SIG-proyecto"

1. TÍTULO
   "Sistema de Información Gerencial para la Gestión de la Biblioteca del Centro Regional Universitario de Barú (CRUBA)"

2. JUSTIFICACIÓN
   Desarrollar e implementar un Sistema de Información Gerencial que optimice la gestión de los recursos bibliográficos y servicios de la biblioteca del CRUBA, mejorando la eficiencia en los procesos administrativos y la experiencia de los usuarios mediante un sistema intuitivo y facil de usar dentro de la biblioteca

3.1 Objetivo General
establecer un sistema de forma robusta e dinamica con el que contantemente se actulize, ofreciendo y producto de innvodora y de alta calidad


 <style>
    :root {
        --primary-color: #3a0ca3;
        --primary-light: #4361ee;
        --secondary-color: #7209b7;
        --accent-color: #4cc9f0;
        --light-color: #f8f9fa;
        --dark-color: #212529;
        --border-radius: 12px;
        --box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        --transition: all 0.3s ease;
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Poppins', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: #f8f9fa;
        color: #333;
        line-height: 1.6;
        min-height: 100vh;
        padding: 1rem;
    }

    .main-container {
        display: flex;
        flex-direction: column;
        max-width: 1200px;
        margin: 0 auto;
        gap: 2rem;

    }

    header {
        padding: 2rem;
        border-radius: var(--border-radius);
        background: linear-gradient(45deg, rgb(93, 178, 226), rgb(93, 226, 95));
        text-align: center;
        box-shadow: var(--box-shadow);
    }


    .container {
        width: 100%;
        /* max-width: 800px; */
        /* margin: 10px; */
        padding: 3rem;
        background-color: #fff;
        border-radius: var(--border-radius);
        box-shadow: var(--box-shadow);
        position: relative;
        overflow: hidden;
    }

    .container::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 8px;
        background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
    }


    .form-title {
        color: var(--light-color);
        font-size: 2.5rem;
        text-align: center;
        margin-bottom: 0.5rem;
        font-weight: 700;
        font-style: bold;
    }

    .form-subtitle {
        color: var(--dark-color);
        text-align: center;
        margin-bottom: 2.5rem;
        font-weight: normal;
        font-size: 1.1rem;
    }

    .highlight {
        color: var(--primary-color);
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
    }

    .content-wrapper {
        background-color: #fff;
        border-radius: var(--border-radius);
        box-shadow: var(--box-shadow);
        overflow: hidden;
        position: relative;
    }

    .content-wrapper::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 8px;
        background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
    }

    .form-section {
        padding: 2rem;
        background-color: transparent;
        border-radius: var(--border-radius);
        margin-bottom: 1rem;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
    }

    .form-section h3 {
        color: var(--primary-color);
        margin-bottom: 1.5rem;
        font-weight: 600;
        position: relative;
        padding-bottom: 0.5rem;
    }

    .form-section h3::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 50px;
        height: 3px;
        background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
        border-radius: 2px;
    }

    .btn-group .btn {
        padding: 0.75rem 1.5rem;
        font-weight: 500;
        border-radius: var(--border-radius);
        transition: var(--transition);
        border: 2px solid transparent;
    }

    .btn-primary {
        background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
        color: white;
        border-color: var(--primary-color);
    }

    .btn-primary:hover {
        background: linear-gradient(90deg, var(--secondary-color), var(--primary-color));
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(66, 99, 235, 0.3);
    }

    .btn-secondary {
        background-color: #f1f3f5;
        color: #495057;
        border-color: #e9ecef;
    }

    .btn-secondary:hover {
        background-color: #e9ecef;
        transform: translateY(-2px);
    }

    .form-control,
    .form-select {
        padding: 0.9rem 1.2rem;
        border: 2px solid #eaeaea;
        border-radius: var(--border-radius);
        transition: var(--transition);
        font-size: 1rem;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: var(--primary-light);
        box-shadow: 0 0 0 4px rgba(67, 97, 238, 0.15);
    }

    .scanner-container {
        width: 100%;
        max-width: 500px;
        margin: 20px auto;
        border: 2px dashed #ccc;
        padding: 1rem;
        border-radius: var(--border-radius);
        background-color: #f9f9f9;
    }

    #scanner-view {
        width: 100%;
        height: 300px;
        background-color: #f0f0f0;
        border-radius: var(--border-radius);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #666;
    }

    .hidden {
        display: none !important;
    }

    .alert {
        padding: 1rem;
        margin-bottom: 1.5rem;
        border-radius: var(--border-radius);
        border: none;
    }

    .alert-danger {
        background-color: #f8d7da;
        color: #721c24;
    }

    .alert-success {
        background-color: #d4edda;
        color: #155724;
    }

    /* Animaciones */
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .content-wrapper {
        animation: fadeIn 0.6s ease-out;
    }

    /* Responsive Design */
    @media (min-width: 768px) {
        .main-container {
            flex-direction: row;
            align-items: flex-start;
        }


        .content-wrapper {
            flex: 2;
        }

        .row {
            display: flex;
            gap: 2rem;
        }

        .col-md-6 {
            flex: 1;
        }
    }

    @media (max-width: 767px) {
        .main-container {
            padding: 0.5rem;
        }

        .form-title {
            font-size: 2rem;
        }

        .form-section {
            padding: 1.5rem;
            margin-bottom: 1rem;
        }

        .btn-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .btn-group .btn {
            width: 100%;
            margin: 0;
        }

        .row {
            flex-direction: column;
        }

        .scanner-container {
            margin: 10px 0;
        }

        #scanner-view {
            height: 250px;
        }
    }

    @media (max-width: 576px) {
        body {
            padding: 0.5rem;
        }

        .form-section {
            padding: 1rem;
        }

        .form-title {
            font-size: 1.8rem;
        }

        .form-subtitle {
            font-size: 1rem;
        }

        header {
            padding: 1.5rem;
        }
    }

    /* Mejoras para botones activos */
    .btn.active {
        background: linear-gradient(90deg, var(--primary-color), var(--secondary-color)) !important;
        color: white !important;
        border-color: var(--primary-color) !important;
        box-shadow: 0 4px 15px rgba(66, 99, 235, 0.3);
    }

    /* Estilos para modales */
    .modal-content {
        border-radius: var(--border-radius);
        border: none;
        box-shadow: var(--box-shadow);
    }

    .modal-header {
        background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
        color: white;
        border-radius: var(--border-radius) var(--border-radius) 0 0;
    }

    .modal-title {
        font-weight: 600;
    }

    .btn-close {
        filter: invert(1);
    }

    /* Estilos para el área de estudiante registrado */
    .welcome-section {
        text-align: center;
        padding: 3rem 2rem;
    }

    .welcome-section h3 {
        color: var(--primary-color);
        font-size: 2rem;
        margin-bottom: 1rem;
    }

    .student-info {
        background-color: #f8f9fa;
        padding: 1.5rem;
        border-radius: var(--border-radius);
        margin: 2rem 0;
        border-left: 4px solid var(--primary-color);
    }

    .action-buttons {
        display: flex;
        flex-direction: column;
        gap: 1rem;
        max-width: 400px;
        margin: 0 auto;
    }

    @media (min-width: 576px) {
        .action-buttons {
            flex-direction: row;
            justify-content: center;
        }
    }
    </style>
