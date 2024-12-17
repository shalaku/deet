import withNuxt from './.nuxt/eslint.config.mjs';

export default withNuxt({
	rules: {
		'vue/html-self-closing': 'off',
		'vue/attributes-order': 'off',
		'vue/order-in-components': 'off',
		'no-unused-vars': 'off',
		// '@typescript-eslint/no-explicit-any': 'off',
		'vue/no-unused-vars': [
			'warn',
			{
				ignorePattern: '^_',
			},
		],
		'@typescript-eslint/no-unused-vars': [
			'warn',
			{
				argsIgnorePattern: '^_',
				varsIgnorePattern: '^_',
				caughtErrorsIgnorePattern: '^_',
			},
		],
		'vue/no-multiple-template-root': [
			'error',
			{
				disallowComments: true,
			},
		],
	},
});
