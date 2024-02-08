import React, { useState } from "react";
import Button from "../../Components/Button";
import toast from "react-hot-toast";

export default function ({ closeRef, project }) {
  const [toolSettings, setToolSettings] = useState({
    useComposer: true,
    usePHPVersion: true,
    usePHPStan: true,
    usePHPCS: true,
    useStyleLine: true,
    useEslint: true,
  });

  const [isLoading, setIsLoading] = useState(false);

  const handleChange = (tool) => {
    setToolSettings((prevSettings) => ({
      ...prevSettings,
      [tool]: !prevSettings[tool],
    }));
  };

  const submitForm = async (e) => {
    e.preventDefault();

    setIsLoading(true);

    const formData = new FormData(e.target);
    formData.set("useComposer", toolSettings.useComposer ? "1" : "0");
    formData.set("usePHPStan", toolSettings.usePHPStan ? "1" : "0");
    formData.set("usePHPCS", toolSettings.usePHPCS ? "1" : "0");
    formData.set("useStyleLine", toolSettings.useStyleLine ? "1" : "0");
    formData.set("usePHPVersion", (toolSettings.usePHPVersion = "1"));
    formData.set("useEslint", toolSettings.useEslint ? "1" : "0");

    try {
      const response = await fetch(`/api/git/clone/${project.id}`, {
        method: "POST",
        body: formData,
      });

      const responseData = await response.json();

      if (responseData.code === 200) {
        const rapportId = responseData.rapportId;
        toast.success("Le rapport a été créé avec succès.");
        closeRef.current.click();
        window.location.href = `/rapport/${rapportId}`;
      } else {
        toast.error("La requête a échoué avec le code :", responseData.code);
      }
    } catch (error) {
      toast.error("Une erreur est survenue lors de la création du rapport.");
    } finally {
      setIsLoading(false);
    }
  };

  return (
    <form onSubmit={submitForm} className="modal-form">
      <hr />
      <div className="d-flex justify-content-center">
        <h6>Analyse Composer</h6>
      </div>
      <div className="form-check form-switch">
        <label className="form-check-label">
          <input
            className="form-check-input"
            type="checkbox"
            name="useComposer"
            checked={toolSettings.useComposer}
            onChange={() => handleChange("useComposer")}
          />
        </label>
        Utiliser Composer Audit
      </div>
      <hr />
      <div className="d-flex justify-content-center">
        <h6>Analyse PHP</h6>
      </div>
      <div className="form-check form-switch">
        <label className="form-check-label">
          <input
            className="form-check-input"
            type="checkbox"
            name="usePHPStan"
            checked={toolSettings.usePHPStan}
            onChange={() => handleChange("usePHPStan")}
          />
        </label>
        Utiliser PHPStan
      </div>
      <div className="form-check form-switch">
        <label className="form-check-label">
          <input
            className="form-check-input"
            type="checkbox"
            name="usePHPCS"
            checked={toolSettings.usePHPCS}
            onChange={() => handleChange("usePHPCS")}
          />
        </label>
        Utiliser PHPCS
      </div>
      <hr />
      <div className="d-flex justify-content-center">
        <h6>Analyse CSS & SCSS</h6>
      </div>
      <div className="form-check form-switch">
        <label className="form-check-label">
          <input
            className="form-check-input"
            type="checkbox"
            name="useStyleLine"
            checked={toolSettings.useStyleLine}
            onChange={() => handleChange("useStyleLine")}
          />
        </label>
        Utiliser Style Line
      </div>
      <hr />
      <div className="d-flex justify-content-center">
        <h6>Analyse JS</h6>
      </div>
      <div className="form-check form-switch">
        <label className="form-check-label">
          <input
            className="form-check-input"
            type="checkbox"
            name="useEslint"
            checked={toolSettings.useEslint}
            onChange={() => handleChange("useEslint")}
          />
        </label>
        Utiliser Eslint
      </div>

      <div className="form-group">
        <div className="form-group">
          <Button text="Créer" loadingText="Création..." isLoading={isLoading} />
        </div>
      </div>
    </form>
  );
}
