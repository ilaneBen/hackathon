import React, { useState } from 'react';

export default function () {
    const [firstName, setFirstName] = useState('');
    const [name, setName] = useState('');
    const [email, setEmail] = useState('');
    const [password, setPassword] = useState('');

    return <div className="user-form"></div>
}
