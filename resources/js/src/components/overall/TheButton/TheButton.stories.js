import GButton from './Index';

export default {
  title: 'Button',
  component: GButton,
  argTypes: {
    color: {
      control: {
        type: 'select',
        options: ['primary', 'secondary']
      }
    },
    type: {
      control: {
        type: 'radio',
        options: ['button', 'submit']
      }
    },
    disabled: {
      control: {
        type: 'boolean'
      }
    },
    xs: {
      control: {
        type: 'boolean'
      }
    },
    sm: {
      control: {
        type: 'boolean'
      }
    },
    lg: {
      control: {
        type: 'boolean'
      }
    },
    center: {
      control: {
        type: 'boolean'
      }
    },
    block: {
      control: {
        type: 'boolean'
      }
    },
    iconRight: {
      control: {
        type: 'text',
      }
    },
    iconLeft: {
      control: {
        type: 'text',
      }
    },
    iconRightSpace: {
      control: {
        type: 'select',
        options: ['mr-1', 'mr-2', 'mr-3', 'mr-4', 'mr-5']
      }
    },
    iconLeftSpace: {
      control: {
        type: 'select',
        options: ['ml-1', 'ml-2', 'ml-3', 'ml-4', 'ml-5']
      }
    },
  }
};

export const Button = (args, { argTypes }) => ({
  props: Object.keys(argTypes),
  components: { GButton },
  template: `
    <g-button
      text="یک دکمه معمولی"
      :color="color"
      :type="type"
      :disabled="disabled"
      :xs="xs"
      :sm="sm"
      :lg="lg"
      :icon-right="iconRight"
      :icon-left="iconLeft"
      :icon-right-space="iconRightSpace"
      :icon-left-space="iconLeftSpace"
      :center="center"
      :block="block"
    />
  `
});