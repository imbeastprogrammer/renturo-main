import _ from 'lodash';
import { ChevronLeft, ChevronRight } from 'lucide-react';
import { Button } from '../ui/button';
import { cn } from '@/lib/utils';
import { usePagination } from '@/hooks/usePagination';

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
        <ul className='flex items-center gap-2'>
            <li>
                <Button
                    size='icon'
                    variant='ghost'
                    disabled={currentPage === 1}
                    onClick={() => onPrevPage(currentPage)}
                >
                    <ChevronLeft />
                </Button>
            </li>
            {pages.map((page) => (
                <li key={page}>
                    <button
                        className={cn(
                            'w-5 border-b border-transparent text-[15px] font-bold transition',
                            {
                                'border-picton-blue text-picton-blue':
                                    currentPage === page,
                            },
                        )}
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
                    </button>
                </li>
            ))}
            <li>
                <Button
                    size='icon'
                    variant='ghost'
                    disabled={currentPage === numberOfPages}
                    onClick={() => onNextPage(currentPage)}
                >
                    <ChevronRight />
                </Button>
            </li>
        </ul>
    );
}

export default Pagination;
