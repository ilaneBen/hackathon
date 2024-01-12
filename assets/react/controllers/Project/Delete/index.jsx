import React from "react";

export default function ({ closeRef, project, csrf }) {
  const submitForm = (e) => {
    e.preventDefault();

    fetch(project.deleteUrl, {
      body: { id: project.id },
      method: "DELETE",
    })
      .then((res) => res.json())
      .then((res) => {
        if (res?.code === 200) {
          toast.success("Le projet a bien supprimé.");
          closeRef.current.click();
        }
      });
  };

  return (
    <form onSubmit={submitForm}>
      <p>
        Attention, vous êtes sur le point de supprimer le projet <strong>{project?.name}</strong>.
      </p>

      <input type="hidden" name="csrf" value={csrf} />

      <button type="submit" className="btn btn-danger">
        Supprimer le projet
      </button>
    </form>
  );
}
