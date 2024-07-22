import axios from 'axios';

const API_URL = `${process.env.REACT_APP_API_BASE_URL}/submit.php`;

export const submitForm = async (formData) => {
  try {
    const response = await axios.post(API_URL, formData, {
      headers: {
        'Content-Type': 'application/json',
      },
    });
    return response.data;
  } catch (error) {
    throw new Error('There was an error submitting the form!');
  }
};