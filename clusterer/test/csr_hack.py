import scipy.sparse
import numpy as np

def csr_row_set_nz_to_val(csr, row, value=0):
    """Set all nonzero elements (elements currently in the sparsity pattern)
    to the given value. Useful to set to 0 mostly.
    """
    # if not isinstance(csr, scipy.sparse.csr_matrix):
    #     raise ValueError('Matrix given must be of CSR format.')
    csr.data[csr.indptr[row]:csr.indptr[row+1]] = value

def csr_rows_set_nz_to_val(csr, rows, value=0):
    for row in rows:
        if row < csr.shape[1]:
            csr_row_set_nz_to_val(csr, row)
        print "done with ", row
    if value == 0:
        csr.eliminate_zeros()