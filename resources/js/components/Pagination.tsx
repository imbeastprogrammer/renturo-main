import _ from 'lodash';
import { useMemo } from 'react';
import { ArrowLeftIcon, ArrowRightIcon } from 'lucide-react';
import { Button } from './ui/button';
import { cn } from '@/lib/utils';

type UsePaginationParams = {
    numberOfPages: number;
    siblingCount?: number;
    currentPage: number;
};

export const usePagination = ({
    numberOfPages,
    siblingCount = 1,
    currentPage,
}: UsePaginationParams) => {
    const paginationRange = useMemo(() => {
        const totalPageNumbers = siblingCount + 7;

        if (totalPageNumbers >= numberOfPages) {
            return _.range(1, totalPageNumbers + 1);
        }

        const leftSiblingIndex = Math.max(currentPage - siblingCount, 1);
        const rightSiblingIndex = Math.min(
            currentPage + siblingCount,
            numberOfPages,
        );

        const shouldShowLeftDots = leftSiblingIndex > 2;
        const shouldShowRightDots = rightSiblingIndex < numberOfPages - 2;

        const firstPageIndex = 1;
        const lastPageIndex = numberOfPages;

        if (!shouldShowLeftDots && shouldShowRightDots) {
            let leftItemCount = 3 + 2 * siblingCount;
            let leftRange = _.range(1, leftItemCount + 1);

            return [...leftRange, 'right-dots', numberOfPages];
        }
        if (shouldShowLeftDots && !shouldShowRightDots) {
            let rightItemCount = 3 + 2 * siblingCount;
            let rightRange = _.range(
                numberOfPages - rightItemCount + 1,
                numberOfPages + 1,
            );
            return [firstPageIndex, 'left-dots', ...rightRange];
        }
        if (shouldShowLeftDots && shouldShowRightDots) {
            let middleRange = _.range(leftSiblingIndex, rightSiblingIndex + 1);
            return [
                firstPageIndex,
                'left-dots',
                ...middleRange,
                'right-dots',
                lastPageIndex,
            ];
        }

        return [];
    }, [numberOfPages, siblingCount, currentPage]);

    return paginationRange;
};

type PaginationProps = {
    numberOfPages: number;
    currentPage: number;
    onPageChange: (page: number) => void;
    onNextPage: (page: number) => void;
    onPrevPage: (page: number) => void;
};

const DotsMap: Record<string, string> = {
    'right-dots': '...',
    'left-dots': '...',
};

function Pagination({
    numberOfPages,
    currentPage = 1,
    onNextPage,
    onPrevPage,
    onPageChange,
}: PaginationProps) {
    const pages = usePagination({
        numberOfPages,
        currentPage: currentPage,
    });

    return (
        <ul className='flex gap-2'>
            <li>
                <Button
                    size='icon'
                    variant='ghost'
                    disabled={currentPage === 1}
                    onClick={() => onPrevPage(currentPage)}
                >
                    <ArrowLeftIcon />
                </Button>
            </li>
            {pages.map((page) => (
                <li key={page}>
                    <Button
                        className={cn({
                            'bg-arylide-yellow hover:bg-arylide-yellow/90':
                                currentPage === page,
                        })}
                        variant={currentPage === page ? 'default' : 'outline'}
                        onClick={() => {
                            if (
                                typeof page === 'string' &&
                                page === 'right-dots'
                            )
                                return onPageChange(numberOfPages);
                            if (
                                typeof page === 'string' &&
                                page === 'left-dots'
                            )
                                return onPageChange(1);
                            if (typeof page === 'number') onPageChange(page);
                        }}
                    >
                        {DotsMap[page] || page}
                    </Button>
                </li>
            ))}
            <li>
                <Button
                    size='icon'
                    variant='ghost'
                    disabled={currentPage === numberOfPages}
                    onClick={() => onNextPage(currentPage)}
                >
                    <ArrowRightIcon />
                </Button>
            </li>
        </ul>
    );
}

export default Pagination;
