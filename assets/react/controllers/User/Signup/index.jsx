import React, { useState } from "react";
import Button from "../../Components/Button";
import toast from "react-hot-toast";

export default function ({ title, csrf, redirectPath, apiPath }) {
  const [firstName, setFirstName] = useState("");
  const [name, setName] = useState("");
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [isLoading, setIsLoading] = useState(false);

  const onFormSubmit = (e) => {
    e.preventDefault();
    setIsLoading(true);

    const formData = new FormData(e.target);

    fetch(apiPath, {
      method: "POST",
      body: formData,
    })
      .then((res) => res.json())
      .then((res) => {
        // If success, redirect the page.
        if (res?.code === 200) {
          window.location.href = redirectPath;
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
          value={password}
          onChange={(e) => setPassword(e.target.value)}
        />
      </div>

      <input type="hidden" name="csrf" value={csrf} />
      <input type="hidden" name="type" value="signup" />

      <div className="form-group col-12">
        <Button text="S'inscrire" loadingText="Inscription..." size="lg" isLoading={isLoading} />
      </div>
    </form>
  );
}
