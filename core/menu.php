<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php">Wikifruit</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#top-navbar" aria-controls="top-navbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="top-navbar">

            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link active" href="index.php">Accueil</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="listfruits.php">Liste des Fruits</a>
                </li>
                
                    <li class="nav-item">
                        <a class="nav-link" href="register.php">Inscription</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">Connexion</a>
                    </li>

                            </ul>

            <!-- Formulaire de recherche en haut à droite -->
            <form class="d-flex" method="GET" action="search.php">
                <input name="q" class="form-control me-2" type="search" placeholder="Chercher un fruit" aria-label="Search">
                <button class="btn btn-outline-success" type="submit"><i class="fas fa-search"></i></button>
            </form>
        </div>
    </div>
</nav>