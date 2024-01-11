import React from "react";

export default function ({ closeRef, project }) {
  console.log(project);

  const handleDelete = () => {
    fetch(project.deleteUrl, {
      body: { id: project.id },
      method: "POST",
    })
      .then((res) => res.json())
      .then((res) => {
        if (res?.code === 200) {
          toast.success("Le projet a bien supprimé.");
          closeRef.current.click();
        }
      });
  };

  return <button onClick={handleDelete} className="btn btn-danger"></button>;
}
