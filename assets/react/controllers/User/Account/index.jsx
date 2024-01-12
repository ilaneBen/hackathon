import React, { useState } from "react";
import clsx from "clsx";

export default function ({ title, editApiPath }) {
  const [firstName, setFirstName] = useState("");
  const [name, setName] = useState("");
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [error, setError] = useState("");
  const [isLoading, setIsLoading] = useState(false);

  const onFormSubmit = (e) => {
    e.preventDefault();

    setError("");
    setIsLoading(true);

    const formData = new FormData(e.target);

    fetch(editApiPath, {
      method: "POST",
      body: formData,
    })
      .then((res) => res.json())
      .then((res) => {
        // If success, redirect the page.
        if (res?.code === 200) {
          window.location.href = redirectPath;
        } else {
          setError(res?.message);
        }
      })
      .catch(() => setError("Une erreur est survenue."))
      .finally(() => setIsLoading(false));
  };

  return (
    <form onSubmit={onFormSubmit} className="row user-form">
      <h1>{title}</h1>

      {error && (
        <div className="alert alert-danger alert-dismissible fade show" role="alert">
          {error}
          <button type="button" className="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      )}

      <div className="form-group col-6">
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

      <div className="form-group col-6">
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

      <div className="form-group col-12">
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

      <div className="form-group col-12">
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
        <button type="submit" className={clsx("btn btn-primary btn-lg", isLoading && "disabled")}>
          {isLoading ? "Création du compte..." : "S'inscrire"}
        </button>
      </div>
    </form>
  );
}
