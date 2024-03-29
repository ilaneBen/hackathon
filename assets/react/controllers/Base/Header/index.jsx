import React, { useEffect, useRef } from "react";
import toast, { Toaster } from "react-hot-toast";
import monkey from "../../Components/ThreeJS";

export default function ({
  flashes,
  isLogged,
  homePath,
  signinPath,
  signupPath,
  projectsPath,
  myAccountPath,
  signoutPath,
  dashboardPath,
  adminUsersPath,
  isAdmin,
}) {
  const ref = useRef();

  useEffect(() => {
    Object.keys(flashes).map((type) => {
      flashes[type].map((message) => {
        if (type === "error") {
          toast.error(message);
        } else if (type === "success") {
          toast.success(message);
        } else {
          toast(message);
        }
      });
    });
  }, []);

  monkey(80, 50, ref, "header");

  return (
    <>
      <Toaster />
      <nav className="navbar navbar-expand-lg bg-body-tertiary">
        <div className="container">
          <a ref={ref} className="navbar-brand" href={homePath}></a>
          <button
            className="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent"
            aria-expanded="false"
            aria-label="Toggle navigation"
          >
            <span className="navbar-toggler-icon"></span>
          </button>
          <div className="collapse navbar-collapse" id="navbarSupportedContent">
            <ul className="navbar-nav me-auto mb-2 mb-lg-0">
              <li className="nav-item">
                <a className="nav-link active" aria-current="page" href={homePath}>
                  Accueil
                </a>
              </li>
              {isLogged && (
                <>
                  <li className="nav-item">
                    <a className="nav-link" href={projectsPath}>
                      Mes projets
                    </a>
                  </li>
                  <li className="nav-item">
                    <a className="nav-link" href={myAccountPath}>
                      Mon compte
                    </a>
                  </li>
                  {isAdmin && (
                    <>
                      <li className="nav-item">
                        <button
                          className="dropdown-toggle nav-link"
                          type="button"
                          data-bs-toggle="dropdown"
                          aria-expanded="false"
                        >
                          Admin
                        </button>
                        <ul className="dropdown-menu">
                          <li className="nav-item">
                            <a className="nav-link" href={dashboardPath}>
                              Dashboard
                            </a>
                          </li>
                          <li className="nav-item">
                            <a className="nav-link" href={adminUsersPath}>
                              Utilisateurs
                            </a>
                          </li>
                        </ul>
                      </li>
                    </>
                  )}
                </>
              )}
            </ul>

            <ul className="navbar-nav ml-auto mb-2 mb-lg-0">
              {isLogged ? (
                <li className="nav-item">
                  <a className="nav-link" href={signoutPath}>
                    Se déconnecter
                  </a>
                </li>
              ) : (
                <>
                  <li className="nav-item">
                    <a className="nav-link" href={signupPath}>
                      Créer un compte
                    </a>
                  </li>
                  <li className="nav-item">
                    <a className="nav-link" href={signinPath}>
                      Se connecter
                    </a>
                  </li>
                </>
              )}
            </ul>
          </div>
        </div>
      </nav>
    </>
  );
}
