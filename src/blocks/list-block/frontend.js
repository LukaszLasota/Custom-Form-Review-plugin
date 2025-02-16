document.addEventListener('DOMContentLoaded', () => {
	const listContainer = document.querySelector('.feedback-list');
	const paginationContainer = document.querySelector('.feedback-pagination');
	const detailContainer = document.querySelector('.feedback-detail');
	let currentPage = 1;

	const loadEntries = (page = 1) => {
		const formData = new FormData();
		formData.append('action', 'get_feedback_entries');
		formData.append('nonce', FeedbackAjax.nonce);
		formData.append('page', page);

		fetch(FeedbackAjax.ajax_url, {
			method: 'POST',
			credentials: 'same-origin',
			body: formData,
		})
			.then(response => response.json())
			.then(data => {
				if (data.success) {
					currentPage = data.data.current_page;
					renderEntries(data.data.entries);
					renderPagination(data.data.current_page, data.data.per_page, data.data.total);
				} else {
					listContainer.innerHTML = `<p>${data.data.message}</p>`;
				}
			})
			.catch(error => {
				console.error('Wystąpił błąd:', error);
			});
	};

	const createAccessibleDivButton = (text, onActivate, disabled = false, extraClasses = '') => {
		const btn = document.createElement('div');
		btn.setAttribute('role', 'button');
		btn.setAttribute('tabindex', disabled ? '-1' : '0');
		btn.textContent = text;
		if (extraClasses) {
			btn.className = extraClasses;
		}
		if (!disabled) {
			btn.addEventListener('click', onActivate);
			btn.addEventListener('keydown', e => {
				if (e.key === 'Enter' || e.key === ' ') {
					e.preventDefault();
					onActivate();
				}
			});
		} else {
			btn.setAttribute('aria-disabled', 'true');
			btn.classList.add('disabled');
		}
		return btn;
	};

	const renderEntries = entries => {
		listContainer.innerHTML = '';
		entries.forEach(entry => {
			const li = document.createElement('li');
			li.className = 'feedback-tile';

			const nameRow = document.createElement('div');
			nameRow.className = 'tile-row tile-row-name';
			nameRow.textContent = `${entry.first_name} ${entry.last_name}`;
			li.appendChild(nameRow);

			const emailRow = document.createElement('div');
			emailRow.className = 'tile-row tile-row-email';
			emailRow.textContent = entry.email;
			li.appendChild(emailRow);

			const subjectRow = document.createElement('div');
			subjectRow.className = 'tile-row tile-row-subject';
			subjectRow.textContent = entry.subject;
			li.appendChild(subjectRow);

			const btn = createAccessibleDivButton(
				'Pokaż całą wiadomość',
				() => loadEntryDetail(entry.id),
				false,
				'tile-row tile-row-button'
			);
			li.appendChild(btn);

			listContainer.appendChild(li);
		});
	};

	const renderPagination = (currentPage, perPage, total) => {
		paginationContainer.innerHTML = '';
		const totalPages = Math.ceil(total / perPage);
		const prevDisabled = currentPage <= 1;
		const nextDisabled = currentPage >= totalPages;

		const prevBtn = createAccessibleDivButton('Poprzedni', () => loadEntries(currentPage - 1), prevDisabled);
		const nextBtn = createAccessibleDivButton('Następny', () => loadEntries(currentPage + 1), nextDisabled);

		paginationContainer.appendChild(prevBtn);
		paginationContainer.appendChild(nextBtn);
	};

	const loadEntryDetail = entryId => {
		const formData = new FormData();
		formData.append('action', 'get_feedback_detail');
		formData.append('nonce', FeedbackAjax.nonce);
		formData.append('entry_id', entryId);

		fetch(FeedbackAjax.ajax_url, {
			method: 'POST',
			credentials: 'same-origin',
			body: formData,
		})
			.then(response => response.json())
			.then(data => {
				if (data.success) {
					renderEntryDetail(data.data);
				} else {
					detailContainer.innerHTML = `<p>${data.data.message}</p>`;
				}
			})
			.catch(() => {
				detailContainer.innerHTML = `<p>${__('Wystąpił błąd. Spróbuj ponownie.', 'custom-form-review')}</p>`;
			});
	};

	const renderEntryDetail = entry => {
		detailContainer.innerHTML = `
			<h3>Szczegóły wiadomości</h3>
			<p><strong>Imię i Nazwisko:</strong> ${entry.first_name} ${entry.last_name}</p>
			<p><strong>Email:</strong> ${entry.email}</p>
			<p><strong>Temat:</strong> ${entry.subject}</p>
			<p><strong>Wiadomość:</strong> ${entry.message}</p>
		`;
		detailContainer.style.border = '1px solid';
	};

	loadEntries(1);
});
