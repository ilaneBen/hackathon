import React, { useRef, useState } from "react";
import clsx from "clsx";

export default function ({ projects, editProjectPath }) {
  const [name, setName] = useState("");
  const [url, setUrl] = useState("");
  const [error, setError] = useState("");
  const [isLoading, setIsLoading] = useState(false);
  const closeRef = useRef();

  const submitForm = (e) => {
    e.preventDefault();

    setIsLoading(true);

    if (!name || !url) {
      setError("Veuillez remplir tous les champs.");
      setIsLoading(false);
      return;
    }

    // Check if the URL is valid.
    if (!url.match(/^(http|https):\/\/[^ "]+$/)) {
      setError("Veuillez entrer une URL valide.");
      setIsLoading(false);
      return;
    }

    const formData = new FormData(e.target);

    fetch(editProjectPath, {
      method: "POST",
      body: formData,
    })
      .then((res) => res.json())
      .then((res) => {
        if (res?.code === 200) {
          closeRef.current.click();
          projects.push(res?.project);
        } else {
          setError(res?.message);
        }
      })
      .catch(() => setError("Une erreur est survenue."))
      .finally(() => setIsLoading(false));
  };

  return (
    <div
      className="modal fade"
      id="createProjectModal"
      tabindex="-1"
      aria-labelledby="exampleModalLabel"
      aria-hidden="true"
    >
      <div className="modal-dialog">
        <div className="modal-content">
          <div className="modal-header">
            <h1 className="modal-title fs-5" id="exampleModalLabel">
              Créer un projet
            </h1>
            <button
              type="button"
              className="btn-close"
              data-bs-dismiss="modal"
              aria-label="Close"
              ref={closeRef}
            ></button>
          </div>
          <div className="modal-body">
            {error && (
              <div className="alert alert-danger" role="alert">
                {error}
              </div>
            )}

            <form onSubmit={submitForm} className="modal-form">
              <div className="form-group">
                <label htmlFor="name">Nom du projet</label>
                <input
                  type="text"
                  id="name"
                  name="name"
                  className="form-control"
                  value={name}
                  onChange={(e) => setName(e.target.value)}
                />
              </div>

              <div className="form-group">
                <label htmlFor="url">Lien du répertoire GitHub</label>
                <input
                  type="text"
                  id="url"
                  name="url"
                  className="form-control"
                  value={url}
                  onChange={(e) => setUrl(e.target.value)}
                />
              </div>

              <div className="form-group">
                <button type="submit" className={clsx("btn btn-primary", isLoading && "disabled")}>
                  {isLoading ? "Modification en cours..." : "Modifier"}
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  );
}
