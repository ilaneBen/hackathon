import React, { useState } from "react";
import toast from "react-hot-toast";
import Button from "../../Components/Button";

export default function ({ closeRef, project, csrf, setFinalProjects }) {
  const [isLoading, setIsLoading] = useState(false);

  const submitForm = (e) => {
    e.preventDefault();

    setIsLoading(true);

    const formData = new FormData(e.target);

    fetch(project.deleteUrl, {
      method: "POST",
      body: formData,
    })
      .then((res) => res.json())
      .then((res) => {
        if (res?.code === 200) {
          toast.success("Le projet a bien supprimé.");
          closeRef.current.click();
          setFinalProjects((finalProjects) => finalProjects.filter((finalProject) => project.id !== finalProject.id));
        }
      })
      .finally(() => setIsLoading(false));
  };

  return (
    <form onSubmit={submitForm}>
      <p>
        Attention, vous êtes sur le point de supprimer le projet <strong>{project?.name}</strong>.
      </p>

      <input type="hidden" name="deleteCsrf" value={csrf} />

      <div className="text-center">
        <Button text="Supprimer le projet" loadingText="Suppression..." variant="danger" isLoading={isLoading} />
      </div>
    </form>
  );
}
