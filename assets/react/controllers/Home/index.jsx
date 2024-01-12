import React, { Fragment, useState } from "react";


export default function () {



  return (
    <div className="Home vw-100">

        <section>
            <div style={{ position: 'relative' }}>
                <img className="w-100" src="/img/parallax-1.jpg" alt="" />
                <div className="col-sm-5 h-100 d-flex flex-column justify-content-between p-5" style={{ position: 'absolute', top: '50%', left: '75%', color: '#ffff', fontSize: '22px', transform: 'translate(-50%, -50%)' }}>
                       <div>
                           <h1 className="text-center mt-5 mb-5">Bienvenue sur l'Application d'Audit !</h1>
                           <h3 className="text-center mt-5 mb-5 " >Sécurisez votre code avec notre expertise Git</h3>
                       </div>
                        <div className="d-flex justify-content-around">
                            <img src="/img/gitlab-logo-200.png" alt="" style={{width: "100px", height: "50px"}}/>
                            <img src="/img/Git-logo.svg.png" alt="" style={{width: "100px", height: "50px"}} />
                            <img src="/img/GitHub-Logo.wine.png" alt="" style={{width: "100px", height: "50px"}}/>
                        </div>
                </div>
            </div>
        </section>
        <section className="section section-fluid bg-light mt-3">
            <div className="container-fluid w-100">
                <p className="text-center ">
                    Notre application offre une solution conviviale pour réaliser des audits de sécurité sur votre code source. Découvrez ci-dessous les fonctionnalités clés de notre service :
                </p>
                <div className="d-flex flex-wrap justify-content-center">
                        <div className="card m-5" style={{width: '28rem',minHeight: "45rem"}}>
                            <img src="/img/git-clone-cb3bc0407b00.png" className="card-img-top mb-3 object-fit-cover" style={{width:"445px", height:"445px"}} alt="..."/>
                            <div className="card-body">
                                <h5 className="card-title">1. Clonage Git Simplifié</h5>
                                <p className="card-text">
                                    Copiez facilement des dépôts Git en fournissant simplement l'URL du dépôt.
                                    L'application gère le processus de clonage, vous laissant plus de temps pour vous
                                    concentrer sur votre code.
                                </p>
                            </div>
                        </div>
                        <div className="card m-5" style={{width: '28rem',minHeight: "45rem"}}>
                            <img src="/img/composer.svg" className="card-img-top mb-3 object-fit-cover" style={{width:"445px", height:"445px"}} alt="..."/>
                            <div className="card-body">
                                <h5 className="card-title">2. Audit Composer Automatique</h5>
                                <p className="card-text">
                                    L’application exécute automatiquement la commande composer audit dans le répertoire
                                    cloné. Détectez rapidement les éventuelles vulnérabilités de sécurité dans les
                                    dépendances PHP de votre projet.
                                </p>
                            </div>
                        </div>
                        <div className="card m-5" style={{width: '28rem',minHeight: "45rem"}}>
                            <img src="/img/php.png" className="card-img-top mb-3 object-fit-cover" style={{width:"445px", height:"445px"}} alt="..."/>
                            <div className="card-body">
                                <h5 className="card-title">3. Analyse PHPStan et PHP CS</h5>
                                <p className="card-text">
                                    Effectuez des analyses statiques du code PHP avec PHPStan pour identifier les
                                    erreurs potentielles. Utilisez PHP CS pour garantir le respect des normes de codage
                                    définies dans vos projets.
                                </p>
                            </div>
                        </div>
                        <div className="card m-5" style={{width: '28rem',minHeight: "45rem"}}>
                            <img src="/img/Centralisation-des-donnees.jpg" className="card-img-top mb-3 object-fit-cover" style={{width:"445px", height:"445px"}} alt="..."/>
                            <div className="card-body">
                                <h5 className="card-title">4. Résultats Centralisés</h5>
                                <p className="card-text">
                                    Stockage des résultats détaillés de chaque analyse en base de données. Consultez les
                                    rapports d'audit à tout moment, offrant une vision claire de la sécurité de vos
                                    dépôts clonés.
                                </p>
                            </div>
                        </div>
                        <div className="card m-5" style={{width: '28rem',minHeight: "45rem"}}>
                            <img src="/img/security.jpg" className="card-img-top mb-3 object-fit-cover" style={{width:"445px", height:"445px"}} alt="..."/>
                            <div className="card-body">
                                <h5 className="card-title">5. Protection de la Vie Privée</h5>
                                <p className="card-text">
                                    Nous ne conservons pas les dépôts Git en mémoire après les analyses. Vos données et
                                    codes sources sont supprimés de manière sécurisée, assurant la confidentialité de
                                    votre travail.
                                </p>
                            </div>
                        </div>
                        <div className="card m-5" style={{width: '28rem',minHeight: "45rem"}}>
                            <img src="/img/points-interrogation.avif" className="card-img-top mb-3 object-fit-cover " style={{width:"445px", height:"445px"}} alt="..."/>
                            <div className="card-body">
                                <h5 className="card-title">6. Comment Utiliser l'Application</h5>
                                <p className="card-text">
                                    Clonage : Accédez à la page de clonage, entrez l'URL du dépôt Git et lancez le
                                    processus de clonage.
                                </p>
                                <p>Audit Composer : Après le clonage, la commande composer audit
                                    s'exécute automatiquement.</p>
                                <p>Analyse PHPStan et PHP CS : Les analyses statiques
                                    améliorent la qualité de votre code. Consultation des Résultats : Explorez les
                                    résultats d'audit directement depuis l'application.</p>
                            </div>
                        </div>
                </div>
                <div className="text-center p-5">
                    <p>
                        Notre application simplifie le processus de clonage et d'audit, assurant la sécurité de vos projets
                        Git.
                    </p>
                    <p> Profitez de cette automatisation pour garantir la robustesse de vos applications. </p>
                    <p>
                        Utilisez notre service dès maintenant et restez serein quant à la sécurité de votre code source !
                    </p>
                </div>

            </div>
        </section>


    </div>
  );
}
