<!-- === includes/header.php === -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Mahanaim Impact Network - Driving SDG 1, 5, and 8 through the Five Pillars: Trade Fairs, Financial Literacy, Mentorship, Humanitarian Support, and Community Building.">
    <meta name="keywords" content="Mahanaim Impact Network, MIN, SDG, trade fair, financial literacy, mentorship, humanitarian support, youth empowerment, women empowerment">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - ' : ''; ?>Lase Impact Network</title>
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800&family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --deep-purple: #4B0082;
            --gold-accent: #D4AF37;
            --white: #FFFFFF;
            --light-gray: #F8F9FA;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Open Sans', sans-serif;
            color: #333;
            overflow-x: hidden;
        }
        
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Montserrat', sans-serif;
            font-weight: 700;
        }
        
        .navbar {
            background: linear-gradient(135deg, var(--deep-purple) 0%, #6A0DAD 100%);
            padding: 1rem 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        
        .navbar-brand {
            font-family: 'Montserrat', sans-serif;
            font-weight: 800;
            font-size: 1.5rem;
            color: var(--white) !important;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .navbar-brand .gold-text {
            color: var(--gold-accent);
        }
        
        .nav-link {
            color: var(--white) !important;
            font-weight: 600;
            margin: 0 0.5rem;
            transition: all 0.3s ease;
            position: relative;
        }
        
        .nav-link:hover {
            color: var(--gold-accent) !important;
            transform: translateY(-2px);
        }
        
        .nav-link.active {
            color: var(--gold-accent) !important;
        }
        
        .btn-gold {
            background: var(--gold-accent);
            color: var(--deep-purple);
            font-weight: 700;
            padding: 0.75rem 2rem;
            border: none;
            border-radius: 50px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(212, 175, 55, 0.3);
        }
        
        .btn-gold:hover {
            background: #c49d2e;
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(212, 175, 55, 0.4);
        }
        
        .btn-blue {
            background: var(--deep-purple);
            color: var(--white);
            font-weight: 700;
            padding: 0.75rem 2rem;
            border: none;
            border-radius: 50px;
            transition: all 0.3s ease;
        }
        
        .btn-blue:hover {
            background: #6A0DAD;
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(13, 59, 102, 0.3);
        }
        
        .section-padding {
            padding: 5rem 0;
        }
        
        .section-title {
            font-size: 2.5rem;
            color: var(--deep-purple);
            margin-bottom: 1rem;
            position: relative;
            display: inline-block;
        }
        
        .section-title::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 0;
            width: 60px;
            height: 4px;
            background: var(--gold-accent);
        }
        
        .card-custom {
            border: none;
            border-radius: 15px;
            overflow: hidden;
            transition: all 0.3s ease;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        }
        
        .card-custom:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.15);
        }
        
        @media (max-width: 768px) {
            .section-title {
                font-size: 1.8rem;
            }
            
            .section-padding {
                padding: 3rem 0;
            }
        }
        .circle-logo {
         width: 100px;
         height: 100px;
         border-radius: 50%;
         object-fit: cover;
         border: none;
         outline: none;
         overflow: hidden;
         display: block; 
       }

    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i>
                <img src="logo.jpeg" class="circle-logo" width="200" height="200">
                </i>
                <span><span class="gold-text">MIN</span> | Mahanaim Impact Network</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
                       <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'about.php' ? 'active' : ''; ?>" href="about.php">About LIN</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'academy.php' ? 'active' : ''; ?>" href="academy.php">The Academy</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'gallery.php' ? 'active' : ''; ?>" href="gallery.php">Impact Gallery</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'partner.php' ? 'active' : ''; ?>" href="partner.php">Partner With Us</a>
                    </li>
                    <li class="nav-item">
                        <a href="register.php" class="btn btn-gold ms-3">Register for Event</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

