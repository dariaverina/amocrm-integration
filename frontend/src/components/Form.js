import React, { useState, useEffect } from 'react';
import { submitForm } from '../api/api';
import Notification from './Notification';
import './Form.css';

const Form = () => {
  const [name, setName] = useState('');
  const [email, setEmail] = useState('');
  const [phone, setPhone] = useState('');
  const [price, setPrice] = useState('');
  const [timeSpent, setTimeSpent] = useState(false);
  const [notification, setNotification] = useState('');

  useEffect(() => {
    const timer = setTimeout(() => {
      setTimeSpent(true);
    }, 30000);
    return () => clearTimeout(timer);
  }, []);

  const handleSubmit = async (e) => {
    e.preventDefault();

    const formData = {
      name,
      email,
      phone,
      price,
      timeSpentOnSite: timeSpent,
    };

    try {
      const response = await submitForm(formData);
      setNotification('Form submitted successfully!');
      console.log(response);
    } catch (error) {
      setNotification('Failed to submit form.');
      console.error(error.message);
    }
  };

  return (
    <div className="form-container">
      {notification && <Notification message={notification} onClose={() => setNotification('')} />}
      <h1>Submit a Deal</h1>
      <form onSubmit={handleSubmit} className="form">
        <input 
          type="text" 
          placeholder="Name" 
          value={name} 
          onChange={(e) => setName(e.target.value)} 
          required 
          className="input"
        />
        <input 
          type="email" 
          placeholder="Email" 
          value={email} 
          onChange={(e) => setEmail(e.target.value)} 
          required 
          className="input"
        />
        <input 
          type="text" 
          placeholder="Phone" 
          value={phone} 
          onChange={(e) => setPhone(e.target.value)} 
          required 
          className="input"
        />
        <input 
          type="number" 
          placeholder="Price" 
          value={price} 
          onChange={(e) => setPrice(e.target.value)} 
          required 
          className="input"
        />
        <button type="submit" className="button">Submit</button>
      </form>
    </div>
  );
};

export default Form;
