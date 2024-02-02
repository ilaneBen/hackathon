import React from "react";
import toast from "react-hot-toast";

export default function ({ closeRef, project, csrf, setFinalProjects }) {
    const submitForm = (e) => {
        e.preventDefault();

        const formData = new FormData(e.target);

        fetch(project.adminDelete, {
            method: "POST",
            body: formData,
        })
            .then((res) => res.json())
            .then((res) => {
                if (res?.code === 200) {
                    toast.success("Le projet a bien supprimé.");
                    closeRef.current.click();
                    setFinalProjects((finalProjects) => finalProjects.filter((finalProject) => project.id !== finalProject.id));
                }
            });
    };

    return (
        <form onSubmit={submitForm}>
            <p>
                Attention, vous êtes sur le point de supprimer le projet <strong>{project?.name}</strong>.
            </p>

            <input type="hidden" name="deleteCsrf" value={csrf} />

            <div className="text-center">
                <button type="submit" className="btn btn-danger">
                    Supprimer le projet
                </button>
            </div>
        </form>
    );
}
