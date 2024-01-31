import React from "react";
import toast from "react-hot-toast";

export default function ({ closeRef, rapport, csrf, project, setRapports }) {
    console.log(rapport);
    const submitForm = (e) => {
        e.preventDefault();

        const formData = new FormData(e.target);
        fetch(rapport.deleteUrl, {
            method: "POST",
            body: formData,
        })
            .then((res) => res.json())
            .then((res) => {
                if (res?.code === 200) {
                    toast.success("Le rapport a bien supprimé.");
                    closeRef.current.click();
                    setRapports((finalRapports) => finalRapports.filter((finalRapport) => rapport.id !== finalRapport.id));
                }
            });
    };

    return (
        <form onSubmit={submitForm}>
            <p>
                Attention, vous êtes sur le point de supprimer le projet <strong>{rapport?.name}</strong>.
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
