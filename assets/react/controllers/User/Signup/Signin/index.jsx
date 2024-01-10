import React, { useState } from "react";

export default function ({ title }) {
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");

  const submitForm = () => {
    console.log(email, password);
  };

  return (
    <div className="row user-form">
      <h1>{title}</h1>

      <div className="form-group col-12">
        <label htmlFor="name">Email</label>
        <input
          type="email"
          id="name"
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

      <div class="form-group col-12">
        <button onClick={submitForm} class="btn btn-primary">
          Se connecter
        </button>
      </div>
    </div>
  );
}
