type SearchableValue = string | number | null | undefined;

/**
 * Composable for local, accent-insensitive text search in lists.
 */
export function useSearch<T extends object>() {
    /**
     * Normalize text to compare values without case/accent differences.
     */
    function normalize(value: string): string {
        return value
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .toLowerCase();
    }

    /**
     * Filter items using search text over the provided fields.
     */
    function filterBySearch<K extends keyof T>(items: T[], searchText: string, fields: K[]): T[] {
        const query = normalize(searchText.trim());

        if (query === '') {
            return items;
        }

        return items.filter((item) =>
            fields.some((field) => {
                const rawValue = item[field] as SearchableValue;

                if (rawValue === null || rawValue === undefined) {
                    return false;
                }

                return normalize(String(rawValue)).includes(query);
            }),
        );
    }

    return {
        normalize,
        filterBySearch,
    };
}
