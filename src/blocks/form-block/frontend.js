document.addEventListener('DOMContentLoaded', () => {
	const form = document.querySelector('.feedback-form');

	if (
		typeof CustomFormReviewUser !== 'undefined' &&
		CustomFormReviewUser !== null &&
		Object.keys(CustomFormReviewUser).length > 0 &&
		form
	) {
		const firstNameField = form.querySelector('input[name="first_name"]');
		const lastNameField  = form.querySelector('input[name="last_name"]');
		const emailField     = form.querySelector('input[name="email"]');

		if (firstNameField) firstNameField.value = CustomFormReviewUser.firstName || '';
		if (lastNameField)  lastNameField.value  = CustomFormReviewUser.lastName || '';
		if (emailField)     emailField.value     = CustomFormReviewUser.email     || '';
	}

	if (form) {
		form.addEventListener('submit', event => {
			event.preventDefault();
			const existingError = document.querySelector('.feedback-error');
			if (existingError) existingError.parentNode.removeChild(existingError);

			const formData = new FormData(form);
			formData.append('action', 'submit_feedback');
			formData.append('nonce', FeedbackAjax.nonce);

			fetch(FeedbackAjax.ajax_url, {
				method: 'POST',
				credentials: 'same-origin',
				body: formData
			})
				.then(response => response.json())
				.then(data => {
					if (data.success) {
						form.outerHTML = `<p>${data.data.message}</p>`;
					} else {
						const errorMessage = document.createElement('p');
						errorMessage.className = 'feedback-error';
						errorMessage.textContent = data.data.message;
						form.parentNode.insertBefore(errorMessage, form.nextSibling);
					}
				})
				.catch(error => console.error('Error:', error));
		});
	}
});
