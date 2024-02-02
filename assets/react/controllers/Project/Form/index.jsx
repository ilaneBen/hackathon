import React, { useEffect, useState } from "react";
import Button from "../../Components/Button";
import toast from "react-hot-toast";

export default function ({ closeRef, project, finalProjects, setFinalProjects, newProjectPath }) {
  const isEditing = !!project;

  const [name, setName] = useState("");
  const [url, setUrl] = useState("");
  const [isLoading, setIsLoading] = useState(false);

  useEffect(() => {
    setName(project ? project.name : "");
    setUrl(project ? project.url : "");
  }, [project]);

  const buttonText = isEditing ? "Modifier" : "Créer";
  const loadingButtonText = isEditing ? "Modification..." : "Création...";
  const apiPath = isEditing ? project?.editUrl : newProjectPath;

  const submitForm = (e) => {
    e.preventDefault();

    setIsLoading(true);

    if (!name || !url) {
      toast.error("Veuillez remplir tous les champs obligatoires.");
      setIsLoading(false);
      return;
    }

    // Check if the URL is a GitHub repository.
    if (!url.match(/^(http|https):\/\/github.com\/[^ "]+$/)) {
      toast.error("Veuillez entrer une URL de répertoire GitHub valide.");
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
          toast.success("Le projet a bien été " + (isEditing ? "modifié" : "créé") + ".");

          if (!isEditing) {
            setFinalProjects((finalProjects) => [...finalProjects, res?.project]);
          } else {
            const finalProjectsCopy = [...finalProjects];
            const index = finalProjectsCopy.findIndex((project) => project.id === res?.project.id);
            finalProjectsCopy[index] = res?.project;
            setFinalProjects(finalProjectsCopy);
          }

          // Reset form.
          setName("");
          setUrl("");
        } else {
          toast.error(res?.message);
        }
      })
      .catch(() => toast.error("Une erreur est survenue."))
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
        <Button text={buttonText} loadingText={loadingButtonText} isLoading={isLoading} />
      </div>
    </form>
  );
}
