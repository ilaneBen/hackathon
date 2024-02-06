import React, { Fragment } from "react";
import "./style.scss";

export default function () {
  const Card = ({ title, text, img }) => {
    return (
      <div className="card col-md-5 col-lg-3 p-0">
        <img src={img} className="card-img-top object-fit-cover card-image" />

        <div className="card-body">
          <h5 className="card-title">{title}</h5>
          <p className="card-text">{text}</p>
        </div>
      </div>
    );
  };

  return (
    <>
      <section className="col-sm-5 d-flex flex-column justify-content-between p-5 home-hero">
        <div className="offset-md-6">
          <div className="texts-container">
            <h1 className="text-center mt-5 mb-5">Bienvenue sur l'Application d'Audit !</h1>
            <h3 className="text-center mt-5 mb-5 ">Sécurisez votre code avec notre expertise Git</h3>
          </div>

          <div className="d-flex justify-content-around images-container">
            <img
              className="object-fit-contain"
              src="/img/gitlab-logo-200.png"
              alt="Logo GitLab"
              style={{ height: "50px" }}
            />

            <img className="object-fit-contain" src="/img/Git-logo.svg.png" alt="Logo Git" style={{ height: "50px" }} />

            <img
              className="object-fit-contain"
              src="/img/GitHub-Logo.wine.png"
              alt="Logo GitHub"
              style={{ height: "50px" }}
            />
          </div>
        </div>
      </section>

      <section>
        <div className="container-fluid">
          <p className="text-center mt-5 mb-5">
            Notre application offre une solution conviviale pour réaliser des audits de sécurité sur votre code source.
            Découvrez ci-dessous les fonctionnalités clés de notre service :
          </p>

          <div className="row justify-content-center m-0" style={{ gap: "40px" }}>
            <Card
              title="1. Clonage Git simplifié"
              text="Copiez facilement des dépôts Git en fournissant simplement l'URL du dépôt. L'application gère le processus de clonage, vous laissant plus de temps pour vous concentrer sur votre code."
              img="/img/git-clone-cb3bc0407b00.png"
            />

            <Card
              title="2. Audit Composer automatique"
              text="L’application exécute automatiquement la commande composer audit dans le répertoire cloné. Détectez rapidement les éventuelles vulnérabilités de sécurité dans les dépendances PHP de votre projet."
              img="/img/composer.svg"
            />

            <Card
              title="3. Analyse PHPStan et PHP CS"
              text="Effectuez des analyses statiques du code PHP avec PHPStan pour identifier les erreurs potentielles. Utilisez PHP CS pour garantir le respect des normes de codage définies dans vos projets."
              img="/img/php.png"
            />

            <Card
              title="4. Résultats centralisés"
              text="Stockage des résultats détaillés de chaque analyse en base de données. Consultez les rapports d'audit à tout moment, offrant une vision claire de la sécurité de vos dépôts clonés."
              img="/img/Centralisation-des-donnees.jpg"
            />

            <Card
              title="5. Protection de la vie privée"
              text="Nous ne conservons pas les dépôts Git en mémoire après les analyses. Vos données et codes sources sont supprimés de manière sécurisée, assurant la confidentialité de votre travail."
              img="/img/security.jpg"
            />

            <Card
              title="6. Comment utiliser l'application"
              text={
                <>
                  Clonage : Accédez à la page de clonage, entrez l'URL du dépôt Git et lancez le processus de clonage.
                  <br />
                  Audit Composer : Après le clonage, la commande composer audit s'exécute automatiquement.
                  <br />
                  Analyse PHPStan et PHP CS : Les analyses statiques améliorent la qualité de votre code.
                  <br />
                  Consultation des Résultats : Explorez les résultats d'audit directement depuis l'application.
                </>
              }
              img="/img/points-interrogation.avif"
            />
          </div>
          <div className="text-center p-5">
            <p>
              Notre application simplifie le processus de clonage et d'audit, assurant la sécurité de vos projets Git.
            </p>
            <p> Profitez de cette automatisation pour garantir la robustesse de vos applications. </p>
            <p>Utilisez notre service dès maintenant et restez serein quant à la sécurité de votre code source !</p>
          </div>
        </div>
      </section>
    </>
  );
}
