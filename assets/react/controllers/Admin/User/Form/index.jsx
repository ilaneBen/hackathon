import React, { useEffect, useState } from "react";
import toast from "react-hot-toast";
import clsx from "clsx";

export default function ({ closeRef, user, finalUsers, setFinalUsers, newUserPath }) {
    const isEditing = !!user;

    const [email, setEmail] = useState("");
    const [name, setName] = useState("");
    const [firstName, setFirstName] = useState("");
    const [password, setPassword] = useState("");
    const [isLoading, setIsLoading] = useState(false);

    useEffect(() => {
        setEmail(user ? user.email : "");
        setName(user ? user.name : "");
        setFirstName(user ? user.firstName : "");
    }, [user]);

    const buttonText = isEditing ? "Modifier" : "Créer";
    const loadingButtonText = isEditing ? "Modification en cours..." : "Création en cours...";
    const apiPath = isEditing ? user?.editUrl : newUserPath;

    const submitForm = (e) => {
        e.preventDefault();

        setIsLoading(true);

        if (!email || !name || !firstName) {
            toast.error("Veuillez remplir tous les champs.");
            setIsLoading(false);
            return;
        }

        if (!isEditing && !password) {
            toast.error("Le mot de passe ne peut pas être vide.");
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
                    toast.success("L'utilisateur a bien été " + (isEditing ? "modifié" : "créé") + ".");

                    if (!isEditing) {
                        setFinalUsers((finalUsers) => [...finalUsers, res?.user]);
                    } else {
                        const finalUsersCopy = [...finalUsers];
                        const index = finalUsersCopy.findIndex((user) => user.id === res?.user.id);
                        finalUsersCopy[index] = res?.user;
                        setFinalUsers(finalUsersCopy);
                    }

                    // Reset form
                    setEmail("");
                    setName("");
                    setFirstName("");
                    setPassword("");
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
                <label htmlFor="name">Nom</label>
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
                <label htmlFor="firstName">Prénom</label>
                <input
                    type="text"
                    id="firstName"
                    name="firstName"
                    className="form-control"
                    value={firstName}
                    onChange={(e) => setFirstName(e.target.value)}
                />
            </div>

            <div className="form-group">
                <label htmlFor="email">Email</label>
                <input
                    type="text"
                    id="email"
                    name="email"
                    className="form-control"
                    value={email}
                    onChange={(e) => setEmail(e.target.value)}
                />
            </div>

            <div className="form-group">
                <label htmlFor="password">Mot de passe</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    className="form-control"
                    value={password}
                    onChange={(e) => setPassword(e.target.value)}
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
