import React, { Fragment, useEffect, useRef, useState } from "react";
import Modal from "../../../Components/Modal/index.jsx";
import Form from "../Form/index.jsx";
import Delete from "../Delete/index.jsx";

export default function ({ title, usersUrl, newUserPath }) {
    const [finalUsers, setFinalUsers] = useState([]);
    const finalTitle = title + (finalUsers.length > 0 ? " (" + finalUsers.length + ")" : "");
    const [user, setUser] = useState(null);
    const [deleteCsrf, setDeleteCsrf] = useState("");
    const [isForm, setIsForm] = useState(false);
    const closeRef = useRef();

    useEffect(() => {
        fetch(usersUrl, {
            method: "GET",
        })
            .then((res) => res.json())
            .then((res) => {
                if (res?.code === 200) {
                    setFinalUsers(res.users);
                }
            });
    }, []);

    const toggleForm = (type, user = null) => {
        setIsForm(true);

        if (type === "edit" && user) {
            setUser(user);
        } else if (type === "new") {
            setUser(null);
        }
    };

    const handleDelete = (user) => {
        setIsForm(false);
        setDeleteCsrf(user.deleteCsrf);
        setUser(user);
    };

    return (
        <div className="users">
            <div className="d-flex justify-content-between mb-4">
                <h1>{finalTitle}</h1>
                <button
                    type="button"
                    className="btn btn-primary h-100 align-self-center"
                    data-bs-toggle="modal"
                    data-bs-target="#projectModal"
                    onClick={() => toggleForm("new")}
                >
                    Créer un utilisateur
                </button>
            </div>

            <div className="table-responsive">
                <table className="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nom</th>
                            <th>Prénom</th>
                            <th>Email</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        {finalUsers.map((user) => (
                            <Fragment key={user.id}>
                                <tr>
                                    <td>{user.id}</td>
                                    <td>{user.name}</td>
                                    <td>{user.firstName}</td>
                                    <td>{user.email}</td>
                                    <td className="actions-row">
                                        <a href={user.adminProjectsUrl} className="btn btn-primary btn-sm">
                                            <i className="bi bi-list-ol"></i>
                                        </a>

                                        <button
                                            type="button"
                                            className="btn btn-secondary btn-sm"
                                            data-bs-toggle="modal"
                                            data-bs-target="#projectModal"
                                            onClick={() => toggleForm("edit", user)}
                                        >
                                            <i className="bi bi-pencil-square"></i>
                                        </button>

                                        <button
                                            type="button"
                                            className="btn btn-danger btn-sm"
                                            data-bs-toggle="modal"
                                            data-bs-target="#projectModal"
                                            onClick={() => handleDelete(user)}
                                        >
                                            <i className="bi bi-trash-fill"></i>
                                        </button>
                                    </td>
                                </tr>
                            </Fragment>
                        ))}
                    </tbody>
                </table>
            </div>

            <Modal
                closeRef={closeRef}
                title={isForm ? (user ? "Modifier un utilisateur" : "Créer un utilisateur") : "Supprimer un utilisateur"}
            >
                {isForm ? (
                    <Form
                        closeRef={closeRef}
                        user={user}
                        newUserPath={newUserPath}
                        finalUsers={finalUsers}
                        setFinalUsers={setFinalUsers}
                    />
                ) : (
                    <Delete closeRef={closeRef} user={user} csrf={deleteCsrf} setFinalUsers={setFinalUsers} />
                )}
            </Modal>
        </div>
    );
}
