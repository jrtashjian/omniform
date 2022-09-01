/**
 * WordPress dependencies
 */
import { createSlotFill } from '@wordpress/components';

const { Fill, Slot } = createSlotFill( 'ExampleSlotFill' );

const ExampleSlotFill = Fill;
ExampleSlotFill.Slot = Slot;

export default ExampleSlotFill;
