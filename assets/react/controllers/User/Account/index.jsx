import React, { useState } from "react";
import toast, { Toaster } from "react-hot-toast";
import Button from "../../Components/Button";

export default function ({ title, user, editApiPath }) {
  const [firstName, setFirstName] = useState(user.firstName);
  const [name, setName] = useState(user.name);
  const [email, setEmail] = useState(user.email);
  const [password, setPassword] = useState("");
  const [isLoading, setIsLoading] = useState(false);

  const onFormSubmit = (e) => {
    e.preventDefault();

    setIsLoading(true);

    const formData = new FormData(e.target);

    if (!firstName || !name || !email) {
      toast.error("Veuillez remplir tous les champs obligatoires.");
      setIsLoading(false);
      return;
    }

    fetch(editApiPath, {
      method: "POST",
      body: formData,
    })
      .then((res) => res.json())
      .then((res) => {
        // If success, redirect the page.
        if (res?.code === 200) {
          toast.success(res?.message);
          setPassword("");
        } else {
          toast.error(res?.message);
        }
      })
      .catch(() => toast.error("Une erreur est survenue."))
      .finally(() => setIsLoading(false));
  };

  return (
    <form onSubmit={onFormSubmit} className="row user-form">
      <h1>{title}</h1>

      <Toaster />

      <div className="form-group col-sm-6">
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

      <div className="form-group col-sm-6">
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

      <div className="form-group col-sm-6">
        <label htmlFor="email">Email</label>
        <input
          type="email"
          id="email"
          name="email"
          className="form-control"
          value={email}
          onChange={(e) => setEmail(e.target.value)}
        />
      </div>

      <div className="form-group col-sm-6">
        <label htmlFor="password">Mot de passe</label>
        <input
          type="password"
          id="password"
          name="password"
          className="form-control"
          placeholder="Laisser vide pour ne pas changer de mot de passe"
          value={password}
          onChange={(e) => setPassword(e.target.value)}
        />
      </div>

      <div className="form-group col-12">
        <Button text="Modifier" loadingText="Modification..." size="lg" isLoading={isLoading} />
      </div>
    </form>
  );
}
