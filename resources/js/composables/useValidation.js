import { ref, reactive } from 'vue';

export function useValidation() {
  const errors = reactive({});
  const touched = reactive({});

  const validateField = (field, value, rules) => {
    delete errors[field];
    touched[field] = true;

    if (!rules || rules.length === 0) return true;

    for (const rule of rules) {
      if (rule === 'required') {
        if (!value || (typeof value === 'string' && value.trim() === '')) {
          errors[field] = 'This field is required';
          return false;
        }
      } else if (rule.startsWith('min:')) {
        const min = parseInt(rule.split(':')[1]);
        if (value && value.length < min) {
          errors[field] = `Must be at least ${min} characters`;
          return false;
        }
      } else if (rule.startsWith('max:')) {
        const max = parseInt(rule.split(':')[1]);
        if (value && value.length > max) {
          errors[field] = `Must not exceed ${max} characters`;
          return false;
        }
      } else if (rule === 'email') {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (value && !emailRegex.test(value)) {
          errors[field] = 'Please enter a valid email address';
          return false;
        }
      } else if (rule === 'url') {
        try {
          new URL(value);
        } catch {
          if (value) {
            errors[field] = 'Please enter a valid URL';
            return false;
          }
        }
      } else if (rule.startsWith('numeric')) {
        if (value && isNaN(value)) {
          errors[field] = 'Must be a number';
          return false;
        }
      }else if (rule === 'not_future') {
        const today = new Date().setHours(0, 0, 0, 0);
        const inputDate = new Date(value).setHours(0, 0, 0, 0);

        if (inputDate > today) {
          errors[field] = 'Future date is not allowed';
          return false;
        }
      }
    }

    return true;
  };

  const validateForm = (formData, rules) => {
    let isValid = true;
    Object.keys(rules).forEach(field => {
      if (!validateField(field, formData[field], rules[field])) {
        isValid = false;
      }
    });
    return isValid;
  };

  const clearErrors = () => {
    Object.keys(errors).forEach(key => delete errors[key]);
    Object.keys(touched).forEach(key => delete touched[key]);
  };

  const setErrors = (serverErrors) => {
    clearErrors();
    Object.assign(errors, serverErrors);
  };

  return {
    errors,
    touched,
    validateField,
    validateForm,
    clearErrors,
    setErrors,
  };
}


