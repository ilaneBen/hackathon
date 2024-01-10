import React, { useRef, useState } from "react";
import clsx from "clsx";

export default function ({ title, csrf }) {
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [error, setError] = useState("");
  const [isLoading, setIsLoading] = useState(false);
  const formRef = useRef(null);

  const submitForm = () => {
    setError("");

    setIsLoading(true);

    const formData = new FormData(formRef.current);

    fetch("/api/signin", {
      method: "POST",
      body: formData,
    })
      .then((res) => res.json())
      .then((res) => {
        console.log("res", res);

        // If success, redirect to signin.
        if (res?.code === 200) {
          console.log("Connexion réussie");
        } else {
          setError(res?.message);
        }
      })
      .catch(() => setError("Une erreur est survenue."))
      .finally(() => setIsLoading(false));
  };

  return (
    <form ref={formRef} className="row user-form">
      <h1>{title}</h1>

      {error && (
        <div className="alert alert-danger alert-dismissible fade show" role="alert">
          {error}
          <button type="button" className="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      )}

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
          value={password}
          onChange={(e) => setPassword(e.target.value)}
        />
      </div>

      <input type="hidden" name="csrf" value={csrf} />

      <div className="form-group col-12">
        <button type="button" onClick={submitForm} className={clsx("btn btn-primary", isLoading && "disabled")}>
          Se connecter
        </button>
      </div>
    </form>
  );
}
