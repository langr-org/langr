
#ifndef	_HASHTABLE_H
#define	_HASHTABLE_H

struct _hash_table;

typedef struct bucket {
	ulong h;			/* Used for numeric indexing. (index or hash value), index = h & nTableMask */
	uint nKeyLength;
	void *pData;
	void *pDataPtr;
	struct bucket *pListNext;
	struct bucket *pListLast;
	struct bucket *pNext;
	struct bucket *pLast;
	char arKey[1]; /* Must be last element */
} Bucket;

typedef struct _hash_table {
	uint nTableSize;		/* 2^n */
	uint nTableMask;		/* 2^n - 1, = 0b111...1 共(n-1)位 */
	uint nNumOfElements;
	ulong nNextFreeElement;
	Bucket *pInternalPointer;	/* Used for element traversal */
	Bucket *pListHead;
	Bucket *pListTail;
	Bucket **arBuckets;
	dtor_func_t pDestructor;
	zend_bool persistent;
	unsigned char nApplyCount;
	zend_bool bApplyProtection;
#if ZEND_DEBUG
	int inconsistent;
#endif
} hash_table;

static inline uint64_t _hash_level(int level, char *arKey, uint32_t nKeyLength)
{
	;
}

static inline uint64_t zend_inline_hash_func(char *arKey, uint32_t nKeyLength)
{
	register uint64_t hash = 5381;

	/* variant with the hash unrolled eight times */
	for (; nKeyLength >= 8; nKeyLength -= 8) {
		hash = ((hash << 5) + hash) + *arKey++;
		hash = ((hash << 5) + hash) + *arKey++;
		hash = ((hash << 5) + hash) + *arKey++;
		hash = ((hash << 5) + hash) + *arKey++;
		hash = ((hash << 5) + hash) + *arKey++;
		hash = ((hash << 5) + hash) + *arKey++;
		hash = ((hash << 5) + hash) + *arKey++;
		hash = ((hash << 5) + hash) + *arKey++;
	}
	switch (nKeyLength) {
		case 7: hash = ((hash << 5) + hash) + *arKey++; /* fallthrough... */
		case 6: hash = ((hash << 5) + hash) + *arKey++; /* fallthrough... */
		case 5: hash = ((hash << 5) + hash) + *arKey++; /* fallthrough... */
		case 4: hash = ((hash << 5) + hash) + *arKey++; /* fallthrough... */
		case 3: hash = ((hash << 5) + hash) + *arKey++; /* fallthrough... */
		case 2: hash = ((hash << 5) + hash) + *arKey++; /* fallthrough... */
		case 1: hash = ((hash << 5) + hash) + *arKey++; break;
		case 0: break;
	}
	return hash;
}

int _zend_hash_init(HashTable *ht, uint nSize, hash_func_t pHashFunction, dtor_func_t pDestructor, int persistent )
{
	uint i = 3;
	Bucket **tmp;

	SET_INCONSISTENT(HT_OK);

	while ((1U << i) < nSize) {
		i++;
	}

	ht->nTableSize = 1 << i;
	ht->nTableMask = ht->nTableSize - 1;
	ht->pDestructor = pDestructor;
	ht->arBuckets = NULL;
	ht->pListHead = NULL;
	ht->pListTail = NULL;
	ht->nNumOfElements = 0;
	ht->nNextFreeElement = 0;
	ht->pInternalPointer = NULL;
	ht->persistent = persistent;
	ht->nApplyCount = 0;
	ht->bApplyProtection = 1;
	
	/* Uses ecalloc() so that Bucket* == NULL */
	if (persistent) {
		tmp = (Bucket **) calloc(ht->nTableSize, sizeof(Bucket *));
		if (!tmp) {
			return FAILURE;
		}
		ht->arBuckets = tmp;
	} else {
		tmp = (Bucket **) calloc(ht->nTableSize, sizeof(Bucket *));
		if (tmp) {
			ht->arBuckets = tmp;
		}
	}
	
	return SUCCESS;
}

/* memcache alloc for HashTables */
#define ALLOC_HASHTABLE(ht)			\
	(ht) = (hash_table *) malloc(sizeof(hash_table))

#define FREE_HASHTABLE(ht)			\
	free(ht)

#define ALLOC_HASHTABLE_REL(ht)			\
	(ht) = (hash_table *) malloc(sizeof(hash_table))

#define FREE_HASHTABLE_REL(ht)			\
	free(ht)

#endif /* _HASHTABLE_H */
