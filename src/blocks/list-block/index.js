import block from './block.json';
import { registerBlockType } from '@wordpress/blocks';
import './editor.scss';
import './style.scss';
import Edit from './edit.js';

registerBlockType(block.name, {
	edit: Edit,
	save: () => null,
} );
