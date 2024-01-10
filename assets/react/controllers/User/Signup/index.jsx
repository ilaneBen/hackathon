import React, { useState } from "react";

export default function ({ title }) {
  const [firstName, setFirstName] = useState("");
  const [name, setName] = useState("");
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");

  const submitForm = () => {
    console.log(firstName, name, email, password);
  };

  return (
    <div className="row user-form">
      <h1>{title}</h1>

      <div className="form-group col-6">
        <label htmlFor="firstName">Prénom</label>
        <input
          type="text"
          id="firstName"
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
          className="form-control"
          value={name}
          onChange={(e) => setName(e.target.value)}
        />
      </div>

      <div className="form-group col-12">
        <label htmlFor="name">Email</label>
        <input
          type="email"
          id="email"
          className="form-control"
          value={email}
          onChange={(e) => setEmail(e.target.value)}
        />
      </div>

      <div className="form-group col-12">
        <label htmlFor="name">Mot de passe</label>
        <input
          type="password"
          id="password"
          className="form-control"
          value={password}
          onChange={(e) => setPassword(e.target.value)}
        />
      </div>

      <div className="form-group col-12">
        <button onClick={submitForm} className="btn btn-primary">
          S'inscrire
        </button>
      </div>
    </div>
  );
}
