module.exports = {
  content: ["./pages/*.{html,js}", "./index.html", "./js/*.js"],
  theme: {
    extend: {
      colors: {
        // Primary Colors - Deep teal for trust and reliability
        primary: {
          DEFAULT: "#006B5C", // teal-800
          50: "#E6F7F5", // teal-50
          100: "#B3E5DF", // teal-100
          200: "#80D3C9", // teal-200
          300: "#4DC1B3", // teal-300
          400: "#1AAF9D", // teal-400
          500: "#009D87", // teal-500
          600: "#008B71", // teal-600
          700: "#007A5B", // teal-700
          800: "#006B5C", // teal-800
          900: "#005A45", // teal-900
        },
        // Secondary Colors - Warm orange for energy and action
        secondary: {
          DEFAULT: "#FF6B35", // orange-500
          50: "#FFF4F0", // orange-50
          100: "#FFE4D6", // orange-100
          200: "#FFD4BC", // orange-200
          300: "#FFC4A2", // orange-300
          400: "#FFB488", // orange-400
          500: "#FF6B35", // orange-500
          600: "#E55A2B", // orange-600
          700: "#CC4921", // orange-700
          800: "#B23817", // orange-800
          900: "#99270D", // orange-900
        },
        // Accent Colors - Golden yellow for highlights and success
        accent: {
          DEFAULT: "#FFB800", // yellow-500
          50: "#FFFBF0", // yellow-50
          100: "#FFF4D6", // yellow-100
          200: "#FFEDBC", // yellow-200
          300: "#FFE6A2", // yellow-300
          400: "#FFDF88", // yellow-400
          500: "#FFB800", // yellow-500
          600: "#E5A600", // yellow-600
          700: "#CC9400", // yellow-700
          800: "#B28200", // yellow-800
          900: "#997000", // yellow-900
        },
        // Background Colors
        background: "#FAFBFC", // gray-50
        surface: "#F1F3F4", // gray-100
        // Text Colors
        text: {
          primary: "#1A1A1A", // gray-900
          secondary: "#5F6368", // gray-600
        },
        // Status Colors
        success: {
          DEFAULT: "#34A853", // green-500
          50: "#E8F5E8", // green-50
          100: "#C8E6C9", // green-100
          200: "#A5D6A7", // green-200
          300: "#81C784", // green-300
          400: "#66BB6A", // green-400
          500: "#34A853", // green-500
          600: "#2E7D32", // green-600
          700: "#1B5E20", // green-700
        },
        warning: {
          DEFAULT: "#FBBC04", // amber-500
          50: "#FFFBF0", // amber-50
          100: "#FFF8E1", // amber-100
          200: "#FFECB3", // amber-200
          300: "#FFE082", // amber-300
          400: "#FFD54F", // amber-400
          500: "#FBBC04", // amber-500
          600: "#F9A825", // amber-600
          700: "#F57F17", // amber-700
        },
        error: {
          DEFAULT: "#EA4335", // red-500
          50: "#FFEBEE", // red-50
          100: "#FFCDD2", // red-100
          200: "#EF9A9A", // red-200
          300: "#E57373", // red-300
          400: "#EF5350", // red-400
          500: "#EA4335", // red-500
          600: "#E53935", // red-600
          700: "#D32F2F", // red-700
        },
        // Border Colors
        border: {
          DEFAULT: "#E8EAED", // gray-300
          light: "#F1F3F4", // gray-200
        },
      },
      fontFamily: {
        // Headlines - Poppins for modern Bengali-friendly sans-serif
        poppins: ['Poppins', 'sans-serif'],
        // Body - Inter for exceptional readability
        inter: ['Inter', 'sans-serif'],
        // Default sans
        sans: ['Inter', 'sans-serif'],
        // Accents - Playfair Display for cultural storytelling
        playfair: ['Playfair Display', 'serif'],
      },
      fontSize: {
        // Custom font sizes for cultural minimalism
        'hero': ['3.5rem', { lineHeight: '1.1', letterSpacing: '-0.02em' }],
        'display': ['2.5rem', { lineHeight: '1.2', letterSpacing: '-0.01em' }],
        'heading': ['2rem', { lineHeight: '1.3' }],
        'subheading': ['1.5rem', { lineHeight: '1.4' }],
        'body-lg': ['1.125rem', { lineHeight: '1.6' }],
        'body': ['1rem', { lineHeight: '1.6' }],
        'body-sm': ['0.875rem', { lineHeight: '1.5' }],
        'caption': ['0.75rem', { lineHeight: '1.4' }],
      },
      spacing: {
        // Cultural minimalism spacing
        '18': '4.5rem',
        '22': '5.5rem',
        '26': '6.5rem',
        '30': '7.5rem',
        '34': '8.5rem',
        '38': '9.5rem',
      },
      boxShadow: {
        // Custom shadows for cultural minimalism
        'primary': '0 2px 8px rgba(0, 107, 92, 0.1)',
        'primary-lg': '0 8px 24px rgba(0, 107, 92, 0.15)',
        'subtle': '0 1px 3px rgba(0, 0, 0, 0.1)',
        'card': '0 2px 8px rgba(0, 107, 92, 0.1)',
        'modal': '0 8px 24px rgba(0, 107, 92, 0.15)',
      },
      borderRadius: {
        // Consistent border radius for cultural minimalism
        'cultural': '0.75rem',
        'cultural-lg': '1rem',
      },
      animation: {
        // Smooth animations for cultural storytelling
        'fade-in': 'fadeIn 300ms ease-out',
        'slide-up': 'slideUp 300ms ease-out',
        'scale-in': 'scaleIn 200ms ease-out',
      },
      keyframes: {
        fadeIn: {
          '0%': { opacity: '0' },
          '100%': { opacity: '1' },
        },
        slideUp: {
          '0%': { transform: 'translateY(10px)', opacity: '0' },
          '100%': { transform: 'translateY(0)', opacity: '1' },
        },
        scaleIn: {
          '0%': { transform: 'scale(0.95)', opacity: '0' },
          '100%': { transform: 'scale(1)', opacity: '1' },
        },
      },
      transitionDuration: {
        // Custom transition durations
        '250': '250ms',
        '350': '350ms',
      },
      transitionTimingFunction: {
        // Custom easing functions
        'smooth': 'cubic-bezier(0.4, 0, 0.2, 1)',
        'cultural': 'cubic-bezier(0.25, 0.46, 0.45, 0.94)',
      },
    },
  },
  plugins: [],
}