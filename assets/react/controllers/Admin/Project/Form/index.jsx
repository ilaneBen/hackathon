import React, { useEffect, useState } from "react";
import toast from "react-hot-toast";
import clsx from "clsx";

export default function ({ closeRef, project, finalProjects, setFinalProjects, newProjectPath }) {
    const isEditing = !!project;

    const [url, setUrl] = useState("");
    const [name, setName] = useState("");
    const [isLoading, setIsLoading] = useState(false);

    useEffect(() => {
        setName(project ? project.name : "");
        setUrl(project ? project.url : "");
    }, [project]);

    const buttonText = isEditing ? "Modifier" : "Créer";
    const loadingButtonText = isEditing ? "Modification en cours..." : "Création en cours...";
    const apiPath = isEditing ? project?.adminEdit : newProjectPath;

    const submitForm = (e) => {
        e.preventDefault();

        setIsLoading(true);

        if (!url || !name) {
            toast.error("Veuillez remplir tous les champs.");
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

                    // Reset form
                    setUrl("");
                    setName("");
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
                <label htmlFor="name">Url du projet</label>
                <input
                    type="text"
                    id="url"
                    name="url"
                    className="form-control"
                    value={url}
                    onChange={(e) => setName(e.target.value)}
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
