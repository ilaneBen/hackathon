import React, { useEffect, useState } from "react";
import clsx from "clsx";

export default function ({ closeRef, project, setFinalProjects, newProjectPath }) {
  const isEditing = !!project;

  const [name, setName] = useState("");
  const [url, setUrl] = useState("");
  const [error, setError] = useState("");
  const [isLoading, setIsLoading] = useState(false);

  useEffect(() => {
    setName(project ? project.name : "");
    setUrl(project ? project.url : "");
  }, [project]);

  const buttonText = isEditing ? "Modifier" : "Créer";
  const loadingButtonText = isEditing ? "Modification en cours..." : "Création en cours...";
  const apiPath = isEditing ? project?.editUrl : newProjectPath;

  const submitForm = (e) => {
    e.preventDefault();

    setIsLoading(true);

    if (!name || !url) {
      setError("Veuillez remplir tous les champs.");
      setIsLoading(false);
      return;
    }

    // Check if the URL is a GitHub repository.
    if (!url.match(/^(http|https):\/\/github.com\/[^ "]+$/)) {
      setError("Veuillez entrer une URL de répertoire GitHub valide.");
      setIsLoading(false);
      return;
    }

    const formData = new FormData(e.target);

    fetch(apiPath, {
      method: "POST",
      body: formData,
    })
      .then((res) => res.json())
      .then((res) => {
        if (res?.code === 200) {
          closeRef.current.click();

          if (!isEditing) {
            setFinalProjects((finalProjects) => [...finalProjects, res?.project]);
          } else {
            setFinalProjects((finalProjects) => {
              const index = finalProjects.findIndex((project) => project.id === res?.project.id);
              finalProjects[index] = res?.project;
              return finalProjects;
            });
          }

          // Reset form.
          setName("");
          setUrl("");
        } else {
          setError(res?.message);
        }
      })
      .catch(() => setError("Une erreur est survenue."))
      .finally(() => setIsLoading(false));
  };

  return (
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
          {isLoading ? loadingButtonText : buttonText}
        </button>
      </div>
    </form>
  );
}
