import GMultiSelect from './Index';

import { shopCategoryOptions } from '../../../data/FiltersMultiSelects';

//👇 This default export determines where your story goes in the story list
export default {
  title: 'Overall',
  component: GMultiSelect,
  argTypes: {
    options: {
      control: {
        type: 'array',
        options: [...shopCategoryOptions],
      },
    },
    size: {
      control: {
        type: 'select',
        options: ['sm', 'lg'],
      },
    },
    stringReturn: {
      control: {
        type: 'boolean',
      },
    },
  },
};

//👇 We create a “template” of how args map to rendering
const Template = (args) => ({
  components: { GMultiSelect },
  props: Object.keys(args),
  data() {
    return {
      model: ''
    };
  },
  template: `
    <div class='col-8'>
      <g-multi-select
        v-model="model"
        :options="options"
        :name="name"
        :string-return="stringReturn"
      />
    </div>
  `,
});

export const MultiSelect = Template.bind({});

MultiSelect.args = {
  options: shopCategoryOptions,
  name: '',
  stringReturn: false,
  size: ''
};