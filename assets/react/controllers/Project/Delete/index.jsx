import React from "react";

export default function ({ closeRef, project, csrf }) {
  const submitForm = (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);

    fetch(project.deleteUrl, {
      body: formData,
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
