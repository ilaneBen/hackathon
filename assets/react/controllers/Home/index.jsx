import React, { Fragment, useState } from "react";


export default function () {



  return (
    <div className="Home vw-100">
        <h2 className="text-center mt-5 mb-5">Bienvenue sur l'Application d'Audit !</h2>
        <section>
            <div style={{ position: 'relative' }}>
                <img className="w-100" src="/img/parallax-1.jpg" alt="" />
                <div className="col-sm-5" style={{ position: 'absolute', top: '50%', left: '75%', color: '#ffff', fontSize: '22px', transform: 'translate(-50%, -50%)' }}>
                    <div className="custom-text-on-image">
                        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit.</p>
                        <p>Dolorum laboriosam magni porro quibusdam sapiente similique temporibus?</p>
                        <p>Dolorem, quia, soluta! Dicta nostrum officiis quas quibusdam.</p>
                    </div>
                </div>
            </div>
        </section>
        <section className="section section-fluid bg-default">
            <div className="container-fluid w-100">

                <p>
                    Notre plateforme vous propose une solution intuitive pour cloner des dépôts Git et effectuer des audits de sécurité sur votre code source. Découvrez ci-dessous les fonctionnalités clés de notre application :
                </p>
                <div className="row">
                    <div className="col-sm-5">
                        <h3>1. Clonage Git Simplifié :</h3>
                        <p>
                            Copiez facilement des dépôts Git en fournissant simplement l'URL du dépôt. L'application gère le clonage, vous laissant plus de temps pour vous concentrer sur votre code.
                        </p>
                        <h3>2. Audit Composer Automatique :</h3>
                        <p>
                            Après le clonage, l'application exécute automatiquement la commande composer audit dans le répertoire cloné. Détectez rapidement les éventuelles vulnérabilités de sécurité dans les dépendances PHP de votre projet.
                        </p>
                        {/* Ajoutez les autres points ici */}
                    </div>
                    <div className="col-sm-7">
                        <h3>3. Résultats Centralisés :</h3>
                        <p>
                            Les résultats détaillés de chaque analyse sont stockés en base de données. Consultez les rapports d'audit à tout moment, offrant une vision claire de la sécurité de vos dépôts clonés.
                        </p>
                        <h3>Comment Utiliser l'Application :</h3>
                        <p>
                            Clonage : Accédez à la page de clonage, entrez l'URL du dépôt Git et lancez le processus de clonage. Audit Composer : Après le clonage, la commande composer audit s'exécute automatiquement. Consultation des Résultats : Explorez les résultats d'audit directement depuis l'application.
                        </p>
                        <p>
                            Notre application simplifie le processus de clonage et d'audit, assurant la sécurité de vos projets Git. Profitez de cette automatisation pour garantir la robustesse de vos applications. Utilisez notre service dès maintenant et restez serein quant à la sécurité de votre code source !
                        </p>
                    </div>
                </div>
            </div>
        </section>

    </div>
  );
}
