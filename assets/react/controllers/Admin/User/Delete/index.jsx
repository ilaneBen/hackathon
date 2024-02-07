import React from "react";
import toast from "react-hot-toast";

export default function ({ closeRef, user, csrf, setFinalUsers }) {
    const submitForm = (e) => {
        e.preventDefault();

        const formData = new FormData(e.target);

        fetch(user.adminDeleteUrl, {
            method: "POST",
            body: formData,
        })
            .then((res) => res.json())
            .then((res) => {
                if (res?.code === 200) {
                    toast.success("L'utilisateur a bien supprimé.");
                    closeRef.current.click();
                    setFinalUsers((finalUsers) => finalUsers.filter((finalUser) => user.id !== finalUser.id));
                }
            });
    };

    return (
        <form onSubmit={submitForm}>
            <p>
                Attention, vous êtes sur le point de supprimer l'utilisateur <strong>{user?.name}</strong>.
            </p>

            <input type="hidden" name="deleteCsrf" value={csrf} />

            <div className="text-center mt-3">
                <button type="submit" className="btn btn-danger">
                    Supprimer l'utilisateur
                </button>
            </div>
        </form>
    );
}
