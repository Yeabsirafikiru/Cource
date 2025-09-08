document.addEventListener("DOMContentLoaded", function () {
  const form = document.getElementById("vitalEventsForm");
  const steps = document.querySelectorAll(".form-step");
  const progressLinks = document.querySelectorAll(".progress-nav a");
  const nextButtons = document.querySelectorAll(".btn-next");
  const prevButtons = document.querySelectorAll(".btn-prev");
  let currentStep = 0;

  showStep(currentStep);
  updateProgress();

  nextButtons.forEach((button) => {
    button.addEventListener("click", function () {
      if (validateStep(currentStep)) {
        currentStep++;
        showStep(currentStep);
        updateProgress();
        updateReview();
      }
    });
  });

  prevButtons.forEach((button) => {
    button.addEventListener("click", function () {
      currentStep--;
      showStep(currentStep);
      updateProgress();
    });
  });

  function showStep(stepIndex) {
    steps.forEach((step, index) => {
      step.classList.toggle("active", index === stepIndex);
    });
  }

  function updateProgress() {
    progressLinks.forEach((link, index) => {
      if (index < currentStep) {
        link.classList.add("completed");
        link.classList.remove("active");
      } else if (index === currentStep) {
        link.classList.add("active");
        link.classList.remove("completed");
      } else {
        link.classList.remove("active", "completed");
      }
    });
  }

  function validateStep(stepIndex) {
    let currentStepFields = steps[stepIndex].querySelectorAll("[required]");
    let isValid = true;

    currentStepFields.forEach((field) => {
      if (!field.value || field.value.trim() === "") {
        if (field.name === "fullName") {
          showError(field, `Please enter your full name.`);
          isValid = false;
        } else if (field.name === "gender") {
          showError(field, `Please select your gender.`);
          isValid = false;
        } else if (field.name === "dateOfBirth") {
          showError(field, `Please enter your birth date.`);
          isValid = false;
        } else if (field.name === "addressLine1") {
          showError(field, `Please enter your address.`);
          isValid = false;
        } else if (field.name === "city") {
          showError(field, `Please enter your city.`);
          isValid = false;
        } else if (field.name === "postalCode") {
          showError(field, `Please enter your postal code.`);
          isValid = false;
        } else if (field.name === "nationality") {
          showError(field, `Please select your nationality.`);
          isValid = false;
        } else if (field.name === "phone") {
          showError(field, `Please enter your phone number.`);
          isValid = false;
        } else if (field.name === "email") {
          showError(field, `Please enter your email address.`);
          isValid = false;
        } else if (field.name === "eventType") {
          showError(field, `Please select the event type.`);
          isValid = false;
        } else if (field.name === "eventDate") {
          showError(field, `Please enter the event date.`);
          isValid = false;
        } else if (field.name === "eventLocation") {
          showError(field, `Please enter the event location.`);
          isValid = false;
        } else {
          showError(field, `${field.name} is required.`);
          isValid = false;
        }
      } else {
        clearError(field);
        if (field.type === "email" && !isValidEmail(field.value)) {
          showError(field, "Please enter a valid email address");
          isValid = false;
        }
        if (field.name === "phone" && !isValidPhone(field.value)) {
          showError(field, "Please enter a valid phone number");
          isValid = false;
        }
      }
    });
    return isValid;
  }

  function showError(field, message) {
    clearError(field);
    const errorDiv = document.createElement("div");
    errorDiv.className = "errorField";
    errorDiv.textContent = message;
    errorDiv.style.color = "#e74c3c";
    errorDiv.style.fontStyle = "italic";
    field.style.borderColor = "#e74c3c";
    field.parentNode.appendChild(errorDiv);
  }

  function clearError(field) {
    const errorDiv = field.parentNode.querySelector(".errorField");
    if (errorDiv) errorDiv.remove();
    field.style.borderColor = "";
  }

  function isValidEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
  }
  function isValidPhone(phone) {
    const re = /^[+]?[0-9\s\-()]{10,}$/;
    return re.test(phone);
  }

  function updateReview() {
    const fieldsToReview = [
      "fullName",
      "dateOfBirth",
      "gender",
      "addressLine1",
      "addressLine2",
      "city",
      "postalCode",
      "nationality",
      "phone",
      "email",
      "eventType",
      "eventDate",
      "eventLocation",
      "eventDescription"
    ];
    fieldsToReview.forEach((field) => {
      const inputElement = document.getElementById(field);
      const reviewElement = document.getElementById(`review-${field}`);
      if (reviewElement && inputElement) {
        reviewElement.textContent = inputElement.value || "";
      }
    });
  }

  form.addEventListener("submit", function (e) {
    // Validate all steps before submitting
    let allValid = true;
    for (let i = 0; i < steps.length; i++) {
      if (!validateStep(i)) {
        allValid = false;
        break;
      }
    }

    if (!allValid) {
      e.preventDefault();
      alert("Please fix the errors before submitting.");
    }
  });
});
