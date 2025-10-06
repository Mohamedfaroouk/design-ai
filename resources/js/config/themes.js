export const colorThemes = {
    indigo: {
        name: 'Indigo',
        gradient: {
            from: 'from-indigo-600',
            via: 'via-purple-600',
            to: 'to-indigo-800'
        },
        colors: {
            primary: 'indigo-600',
            secondary: 'purple-600',
            hover: 'indigo-700',
            focus: 'indigo-500',
            ring: 'indigo-300',
            light: 'indigo-50',
            border: 'indigo-200'
        }
    },
    blue: {
        name: 'Blue',
        gradient: {
            from: 'from-blue-600',
            via: 'via-cyan-600',
            to: 'to-blue-800'
        },
        colors: {
            primary: 'blue-600',
            secondary: 'cyan-600',
            hover: 'blue-700',
            focus: 'blue-500',
            ring: 'blue-300',
            light: 'blue-50',
            border: 'blue-200'
        }
    },
    purple: {
        name: 'Purple',
        gradient: {
            from: 'from-purple-600',
            via: 'via-pink-600',
            to: 'to-purple-800'
        },
        colors: {
            primary: 'purple-600',
            secondary: 'pink-600',
            hover: 'purple-700',
            focus: 'purple-500',
            ring: 'purple-300',
            light: 'purple-50',
            border: 'purple-200'
        }
    },
    pink: {
        name: 'Pink',
        gradient: {
            from: 'from-pink-600',
            via: 'via-rose-600',
            to: 'to-pink-800'
        },
        colors: {
            primary: 'pink-600',
            secondary: 'rose-600',
            hover: 'pink-700',
            focus: 'pink-500',
            ring: 'pink-300',
            light: 'pink-50',
            border: 'pink-200'
        }
    },
    green: {
        name: 'Green',
        gradient: {
            from: 'from-green-600',
            via: 'via-emerald-600',
            to: 'to-green-800'
        },
        colors: {
            primary: 'green-600',
            secondary: 'emerald-600',
            hover: 'green-700',
            focus: 'green-500',
            ring: 'green-300',
            light: 'green-50',
            border: 'green-200'
        }
    },
    orange: {
        name: 'Orange',
        gradient: {
            from: 'from-orange-600',
            via: 'via-amber-600',
            to: 'to-orange-800'
        },
        colors: {
            primary: 'orange-600',
            secondary: 'amber-600',
            hover: 'orange-700',
            focus: 'orange-500',
            ring: 'orange-300',
            light: 'orange-50',
            border: 'orange-200'
        }
    },
    red: {
        name: 'Red',
        gradient: {
            from: 'from-red-600',
            via: 'via-rose-600',
            to: 'to-red-800'
        },
        colors: {
            primary: 'red-600',
            secondary: 'rose-600',
            hover: 'red-700',
            focus: 'red-500',
            ring: 'red-300',
            light: 'red-50',
            border: 'red-200'
        }
    }
}

export const getThemeClasses = (themeName) => {
    return colorThemes[themeName] || colorThemes.indigo
}
