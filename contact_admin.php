<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contacter l'administrateur</title>

    <link rel="stylesheet" href="../../assets/css/contact_admin.css">
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>

<div class="container">

    <!-- LEFT -->
    <div class="left-side">

        <div class="overlay"></div>

        <div class="left-content">

            <img src="../../assets/images/logo.png" class="logo">

            <h1>UATM GASA FORMATION</h1>

            <div class="line"></div>

            <p class="subtitle">GESTION DES MÉMOIRES</p>

            <div class="circle-icon">
                <i class="fa-regular fa-user"></i>
            </div>

            <h2>Contactez l’administrateur</h2>

            <p class="description">
                Besoin d’aide ? Remplissez ce formulaire et notre équipe
                vous répondra dans les plus brefs délais.
            </p>

        </div>

    </div>

    <!-- RIGHT -->
    <div class="right-side">

        <div class="form-header">

            <div class="header-icon">
                <i class="fa-regular fa-envelope"></i>
            </div>

            <div>
                <h1>Contactez l’administrateur</h1>
                <p>
                    Envoyez-nous votre message,
                    nous vous répondrons rapidement.
                </p>
            </div>

        </div>


        <?php if(isset($_SESSION['success'])): ?>

            <div class="success-box">

                <?= $_SESSION['success']; ?>

            </div>

        <?php unset($_SESSION['success']); endif; ?>

        <?php if(isset($_SESSION['error'])): ?>

            <div class="error-box">

                <?= $_SESSION['error']; ?>

            </div>

        <?php unset($_SESSION['error']); endif; ?>
        
        <form
            method="POST"
            action="../../controllers/contact_admin_controller.php">

            <div class="double-input">

                <div class="input-group">
                    <label>Nom complet</label>

                    <div class="input-box">
                        <i class="fa-regular fa-user"></i>
                        <input
                            type="text"
                            name="nom"
                            placeholder="Votre nom complet"
                            required>
                    </div>
                </div>

                <div class="input-group">
                    <label>Email</label>

                    <div class="input-box">
                        <i class="fa-regular fa-envelope"></i>
                        <input
                            type="email"
                            name="email"
                            placeholder="example@uatm.ga"
                            required>
                    </div>
                </div>

            </div>

            <div class="input-group">
                <label>Sujet</label>

                <div class="input-box">
                    <i class="fa-solid fa-tag"></i>

                    <select name="sujet" required>
                        <option>Choisissez un sujet</option>
                        <option>Problème de connexion</option>
                        <option>Mot de passe oublié</option>
                        <option>Erreur de mémoire</option>
                        <option>Autre</option>
                    </select>
                </div>
            </div>

            <div class="input-group">
                <label>Message</label>

                <div class="textarea-box">
                    <i class="fa-regular fa-message"></i>

                    <textarea
                        name="message"
                        placeholder="Décrivez votre demande en détail..."
                        required></textarea>
                </div>
            </div>

            <button
                type="submit"
                name="send_message"
                class="send-btn">

                <i class="fa-regular fa-paper-plane"></i>

                Envoyer le message

            </button>

        </form>

        <div class="info-box">

            <i class="fa-solid fa-circle-info"></i>

            <div>
                <h3>Information</h3>

                <p>
                    Nous nous engageons à vous répondre
                    dans les plus brefs délais.
                </p>
            </div>

        </div>

        <div class="bottom-links">

            <a href="../../login.php">
                <i class="fa-solid fa-arrow-left"></i>
                Retour à la connexion
            </a>

            <a href="../../login.php">
                Connectez-vous ici
            </a>

        </div>

    </div>

</div>

</body>
</html>