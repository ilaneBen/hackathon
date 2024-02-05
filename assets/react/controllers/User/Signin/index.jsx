import React, { useState } from "react";
import toast from "react-hot-toast";
import Button from "../../Components/Button";

export default function ({ title, csrf, redirectPaths, apiPath }) {
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
        if (res?.code === 200) {
          window.location = redirectPaths[res?.role];
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
      <input type="hidden" name="type" value="signin" />

      <div className="form-group col-12">
        <Button text="Se connecter" loadingText="Connexion..." size="lg" isLoading={isLoading} />
      </div>
    </form>
  );
}
