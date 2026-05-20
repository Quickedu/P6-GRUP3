/**
 * Composable that returns helpers to format a date as relative time in Catalan.
 */
export function useRelativeTime() {
    const MS_PER_DAY = 1000 * 60 * 60 * 24;

    /**
     * Convert a date string into a relative label (Avui, Ahir, Ultims 7 dies, etc.).
     */
    const getRelativeTime = (dateString: string | null): string => {
        if (!dateString) {
            return 'Mai';
        }

        const date = new Date(dateString);
        const now = new Date();

        // Compare calendar days to avoid DST/timezone off-by-one issues.
        const todayUtc = Date.UTC(
            now.getFullYear(),
            now.getMonth(),
            now.getDate(),
        );
        const dateUtc = Date.UTC(
            date.getFullYear(),
            date.getMonth(),
            date.getDate(),
        );
        const diffDays = Math.floor((todayUtc - dateUtc) / MS_PER_DAY);

        // Avui (today)
        if (diffDays === 0) {
            return 'Avui';
        }

        // Ahir (yesterday)
        if (diffDays === 1) {
            return 'Ahir';
        }

        // Últims 7 dies (last 7 days)
        if (diffDays > 1 && diffDays <= 7) {
            return 'Últims 7 dies';
        }

        // Últim mes (last month - up to 30 days)
        if (diffDays > 7 && diffDays <= 30) {
            return 'Últim mes';
        }

        // Últim any (last year - up to 365 days)
        if (diffDays > 30 && diffDays <= 365) {
            return 'Últim any';
        }

        // Més d'un any (more than a year)
        return "Més d'un any";
    };

    return {
        getRelativeTime,
    };
}
