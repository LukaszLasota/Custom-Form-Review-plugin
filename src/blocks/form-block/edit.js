import { __ } from '@wordpress/i18n';
import { useBlockProps } from '@wordpress/block-editor';
import { TextControl, Button } from '@wordpress/components';
import { useState, useEffect } from '@wordpress/element';
import './editor.scss';

const Edit = () => {
	const blockProps = useBlockProps();
	const [ firstName, setFirstName ] = useState('');
	const [ lastName, setLastName ] = useState('');
	const [ email, setEmail ] = useState('');
	const [ subject, setSubject ] = useState('');
	const [ message, setMessage ] = useState('');
	const [ responseMessage ] = useState('');

	useEffect(() => {
		if ( typeof CustomFormReviewUser !== 'undefined' ) {
			setFirstName(CustomFormReviewUser.firstName || '');
			setLastName(CustomFormReviewUser.lastName || '');
			setEmail(CustomFormReviewUser.email || '');
		}
	}, []);

	return (
		<div { ...blockProps }>
			<h2>{ __( 'Prześlij swoją opinię', 'custom-form-review' ) }</h2>
			{ responseMessage ? (
				<p>{ responseMessage }</p>
			) : (
				<form className="feedback-form">
					<TextControl
						label={ __( 'Imię', 'custom-form-review' ) }
						value={ firstName }
						onChange={ ( value ) => setFirstName( value ) }
					/>
					<TextControl
						label={ __( 'Nazwisko', 'custom-form-review' ) }
						value={ lastName }
						onChange={ ( value ) => setLastName( value ) }
					/>
					<TextControl
						label={ __( 'Email', 'custom-form-review' ) }
						value={ email }
						onChange={ ( value ) => setEmail( value ) }
					/>
					<TextControl
						label={ __( 'Temat', 'custom-form-review' ) }
						value={ subject }
						onChange={ ( value ) => setSubject( value ) }
					/>
					<textarea
						placeholder={ __( 'Wiadomość', 'custom-form-review' ) }
						value={ message }
						onChange={ ( event ) => setMessage( event.target.value ) }
						rows="5"
					/>
					<Button isPrimary type="submit" disabled>
						{ __( 'Wyślij', 'custom-form-review' ) }
					</Button>
				</form>
			) }
		</div>
	);
};

export default Edit;
