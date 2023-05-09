// Base.
import TomSelect from 'tom-select/dist/esm/tom-select';
import './tom-select/scss/tom-select.default.scss';

// Add plugin example.
// And you need to uncomment the style line in ./tom-select/scss/tom-select.scss
import TomSelectDropdownHeader from 'tom-select/dist/esm/plugins/checkbox_options/plugin';

TomSelect.define( 'checkbox_options', TomSelectDropdownHeader );

export default TomSelect;
