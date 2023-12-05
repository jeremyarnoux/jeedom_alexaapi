module.exports = {
  'env': {
    'browser': false,
    'commonjs': true,
    'es6': true,
    'jasmine': true,
    'jest': true,
    'mocha': true,
    'node': true
  },
  'extends': 'eslint:recommended',
  'parser': '@babel/eslint-parser',
  'parserOptions': {
    'sourceType': 'module',
    'requireConfigFile': false
  },
  'rules': {
    'arrow-parens': [
      'error',
      'always'
    ],
    'arrow-spacing': 'off',
    'block-scoped-var': 'error',
    'block-spacing': [
      'off',
      'always'
    ],
    'brace-style': [
      'off',
      '1tbs'
    ],
    'comma-dangle': [
      'error',
      'always-multiline'
    ],
    'comma-spacing': 'off',
    'comma-style': [
      'error',
      'last'
    ],
    'computed-property-spacing': [
      'error',
      'never'
    ],
    'curly': 'error',
    'dot-notation': 'error',
    'eol-last': 'error',
    'func-call-spacing': [
      'error',
      'never'
    ],
    'implicit-arrow-linebreak': [
      'error',
      'beside'
    ],
    'indent': [
      'off',
      2,
      {
        'ArrayExpression': 'first',
        'CallExpression': {
          'arguments': 'first'
        },
        'FunctionDeclaration': {
          'parameters': 'first'
        },
        'FunctionExpression': {
          'parameters': 'first'
        },
        'ObjectExpression': 'first',
        'SwitchCase': 1
      }
    ],
    'key-spacing': [
      'off',
      {
        'afterColon': true,
        'beforeColon': false,
        'mode': 'strict'
      }
    ],
    'keyword-spacing': [
      'off',
      {
        'after': true,
        'before': true
      }
    ],
    'linebreak-style': [
      'error',
      'unix'
    ],
    'lines-between-class-members': [
      'error',
      'always'
    ],
    'max-len': [
      'off',
      {
        'code': 80,
        'ignoreTemplateLiterals': true
      }
    ],
    'multiline-ternary': [
      'error',
      'always-multiline'
    ],
    'no-console': 0,
    'no-duplicate-imports': 'error',
    'no-eval': 'error',
    'no-floating-decimal': 'error',
    'no-implicit-globals': 'error',
    'no-implied-eval': 'error',
    'no-lonely-if': 'error',
    'no-multi-spaces': [
      'error',
      {
        'ignoreEOLComments': true
      }
    ],
    'no-multiple-empty-lines': 'off',
    'no-prototype-builtins': 'off',
    'no-return-assign': 'error',
    'no-script-url': 'error',
    'no-self-compare': 'error',
    'no-sequences': 'error',
    'no-shadow-restricted-names': 'error',
    'no-tabs': 'off',
    'no-trailing-spaces': 'off',
    'no-undefined': 'off',
    'no-unmodified-loop-condition': 'error',
    'no-unused-vars': [
      'error',
      {
        'argsIgnorePattern': '^_',
        'varsIgnorePattern': '^_'
      }
    ],
    'no-useless-computed-key': 'error',
    'no-useless-concat': 'error',
    'no-useless-constructor': 'off',
    'no-useless-return': 'error',
    'no-var': 'off',
    'no-void': 'error',
    'no-whitespace-before-property': 'error',
    'object-curly-newline': [
      'error',
      {
        'consistent': true
      }
    ],
    'object-curly-spacing': [
      'error',
      'never'
    ],
    'object-property-newline': [
      'error',
      {
        'allowMultiplePropertiesPerLine': true
      }
    ],
    'operator-linebreak': [
      'error',
      'after'
    ],
    'padded-blocks': [
      'off',
      {
        'blocks': 'never'
      }
    ],
    'prefer-const': 'error',
    'prefer-template': 'off',
    'quote-props': [
      'off',
      'as-needed'
    ],
    'quotes': [
      'off',
      'single',
      {
        'allowTemplateLiterals': true
      }
    ],
    'semi': [
      'error',
      'always'
    ],
    'semi-spacing': [
      'off',
      {
        'after': true,
        'before': false
      }
    ],
    'semi-style': [
      'error',
      'last'
    ],
    'space-before-blocks': [
      'off',
      'always'
    ],
    'space-before-function-paren': [
      'error',
      {
        'anonymous': 'never',
        'asyncArrow': 'always',
        'named': 'never'
      }
    ],
    'space-in-parens': [
      'off',
      'never'
    ],
    'space-infix-ops': 'off',
    'space-unary-ops': [
      'error',
      {
        'nonwords': false,
        'words': true
      }
    ],
    'spaced-comment': [
      'off',
      'never'
    ],
    'switch-colon-spacing': [
      'off',
      {
        'after': true,
        'before': false
      }
    ],
    'template-curly-spacing': [
      'error',
      'never'
    ],
    'yoda': 'error'
  }
};
