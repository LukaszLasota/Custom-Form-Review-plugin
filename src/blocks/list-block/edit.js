import { __ } from '@wordpress/i18n';
import { useBlockProps } from '@wordpress/block-editor';
import { useState, useEffect } from '@wordpress/element';

const AccessibleButton = ({ text, onActivate, disabled = false, extraClasses = '' }) => {
	const handleKeyDown = (e) => {
		if (e.key === 'Enter' || e.key === ' ') {
			e.preventDefault();
			onActivate();
		}
	};

	return (
		<div
			role="button"
			tabIndex={disabled ? -1 : 0}
			className={`${extraClasses} ${disabled ? 'disabled' : ''}`}
			onClick={!disabled ? onActivate : undefined}
			onKeyDown={!disabled ? handleKeyDown : undefined}
			aria-disabled={disabled ? 'true' : 'false'}
		>
			{text}
		</div>
	);
};

const Edit = () => {
	const blockProps = useBlockProps();
	const [ entries, setEntries ] = useState([]);
	const [ currentPage, setCurrentPage ] = useState(1);
	const [ selectedEntry, setSelectedEntry ] = useState(null);
	const [ errorMessage, setErrorMessage ] = useState('');
	const [ hasMore, setHasMore ] = useState(true);

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
					setEntries(data.data.entries);
					setCurrentPage(data.data.current_page);
					const total = data.data.total;
					const perPage = data.data.per_page;
					setHasMore(data.data.current_page * perPage < total);
				} else {
					setErrorMessage(data.data.message);
				}
			})
			.catch(() => {
				setErrorMessage(__('Wystąpił błąd. Spróbuj ponownie.', 'custom-form-review'));
			});
	};

	const loadEntryDetail = (entryId) => {
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
			.then(result => {
				if (result.success) {
					setSelectedEntry(result.data);
				} else {
					setErrorMessage(result.data.message);
				}
			})
			.catch(() => {
				setErrorMessage(__('Błąd, sporóbuj ponownie.', 'custom-form-review'));
			});
	};

	useEffect(() => {
		loadEntries(1);
	}, []);

	const renderEntries = (entries) => {
		return (
			<ul className="feedback-list">
				{entries.map(entry => (
					<li key={entry.id} className="feedback-tile">
						<div className="tile-row tile-row-name">
							{entry.first_name} {entry.last_name}
						</div>
						<div className="tile-row tile-row-email">
							{entry.email}
						</div>
						<div className="tile-row tile-row-subject">
							{entry.subject}
						</div>
						<AccessibleButton
							text="Pokaż całą wiadomość"
							onActivate={() => loadEntryDetail(entry.id)}
							extraClasses="tile-row tile-row-button"
						/>
					</li>
				))}
			</ul>
		);
	};

	const renderPagination = (currentPage, perPage, total) => {
		const totalPages = Math.ceil(total / perPage);
		return (
			<div className="feedback-pagination">
				<AccessibleButton
					text={__('Poprzedni', 'custom-form-review')}
					onActivate={() => loadEntries(currentPage - 1)}
					disabled={currentPage <= 1}
				/>
				<AccessibleButton
					text={__('Następny', 'custom-form-review')}
					onActivate={() => loadEntries(currentPage + 1)}
					disabled={!hasMore}
				/>
			</div>
		);
	};

	const renderEntryDetail = (entry) => {
		return (
			<div className="feedback-detail" style={{ border: '1px solid' }}>
				<h3>{__('Szczegóły wiadomości', 'custom-form-review')}</h3>
				<p><strong>{__('Imię i Nazwisko:', 'custom-form-review')}</strong> {entry.first_name} {entry.last_name}</p>
				<p><strong>{__('Email: ', 'custom-form-review')}</strong> {entry.email}</p>
				<p><strong>{__('Temat: ', 'custom-form-review')}</strong> {entry.subject}</p>
				<p><strong>{__('Wiadomość: ', 'custom-form-review')}</strong> {entry.message}</p>
			</div>
		);
	};

	return (
		<div {...blockProps}>
			<h2>{__('Lista wiadomości', 'custom-form-review')}</h2>
			{errorMessage && <p>{errorMessage}</p>}
			<div className="feedback-list-container">
				{renderEntries(entries)}
			</div>
			{renderPagination(currentPage, entries.length ? entries[0].per_page : 10, entries.total || 0)}
			{selectedEntry && renderEntryDetail(selectedEntry)}
		</div>
	);
};

export default Edit;
