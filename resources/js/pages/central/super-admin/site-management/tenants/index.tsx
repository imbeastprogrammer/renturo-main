import { useState } from 'react';
import { PlusIcon } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Link, router } from '@inertiajs/react';

import { useSearchParams } from '@/hooks/useSearchParams';
import { Tenant } from '@/types/tenant';
import SuperAdminLayout from '@/layouts/SuperAdminLayout';
import Searchbar from './components/Searchbar';
import TenantsTable from './components/TenantsTable';
import Pagination from '@/components/super-admin/Pagination';

type TenantsProps = {
    tenants: PaginatedTenant;
};

type PaginatedTenant = {
    current_page: number;
    data: Tenant[];
    last_page: number;
    next_page_url: string | null;
    prev_page_url: string | null;
};

function Tenants({ tenants }: TenantsProps) {
    const { pathname } = window.location;
    const [currentPage, setCurrentPage] = useState(1);
    const { searchParams, queryParams } = useSearchParams();
    const searchTerm = searchParams.get('searchTerm') || '';
    const recordsCount = tenants.data.length;

    const handleNextPage = () => {
        if (tenants.next_page_url)
            router.replace(tenants.next_page_url, { data: { searchTerm } });
    };

    const handlePrevPage = () => {
        if (tenants.prev_page_url)
            router.replace(tenants.prev_page_url, { data: { searchTerm } });
    };

    const handlePageChange = (page: number) => {
        router.replace(pathname, { data: { searchTerm, page } });
    };

    return (
        <div className='h-full bg-[#f0f0f0] p-4'>
            <div className='grid h-full grid-rows-[auto_1fr_auto] gap-4 rounded-xl bg-white p-4 shadow-lg'>
                <div className='flex items-center justify-between'>
                    <Searchbar
                        value={searchTerm}
                        onChange={(e) =>
                            router.replace(pathname, {
                                data: {
                                    ...queryParams,
                                    searchTerm: e.target.value,
                                },
                            })
                        }
                    />
                    <div className='flex items-center gap-4'>
                        <div>
                            <span className='text-[20px] font-semibold text-[#2E3436]/80'>
                                2
                            </span>{' '}
                            <span className='text-[16px] text-[#2E3436]/50'>
                                Record(s) found
                            </span>
                        </div>
                        <div>
                            <Link href='/super-admin/site-management/tenants/create'>
                                <Button className='gap-2 bg-[#84C58A] text-[15px] font-medium hover:bg-[#84C58A]/90'>
                                    <PlusIcon className='h-4 w-4' /> Create
                                </Button>
                            </Link>
                        </div>
                    </div>
                </div>
                <TenantsTable tenants={tenants.data} />
                <div className='flex items-center justify-between'>
                    <div className='text-[15px] font-medium text-black/50'>
                        Showing {recordsCount} record(s) of page{' '}
                        {tenants.current_page}
                    </div>
                    <Pagination
                        currentPage={currentPage}
                        numberOfPages={tenants.last_page}
                        onNextPage={handleNextPage}
                        onPrevPage={handlePrevPage}
                        onPageChange={handlePageChange}
                    />
                    <div className='flex items-center gap-2 text-[15px] font-medium text-black/50'>
                        <span>Page</span>
                        <Input
                            value={currentPage}
                            className='h-8 w-16 text-center'
                            type='number'
                            onChange={(e) =>
                                setCurrentPage(Number(e.target.value))
                            }
                        />
                        <span>of {100}</span>
                    </div>
                </div>
            </div>
        </div>
    );
}

Tenants.layout = (page: any) => <SuperAdminLayout>{page}</SuperAdminLayout>;

export default Tenants;
